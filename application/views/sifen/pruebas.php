<?php
$h = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
$badge_estado = [
    'aprobado' => 'success', 'aprobado_obs' => 'success', 'rechazado' => 'danger',
    'en_lote' => 'info', 'cancelado' => 'dark', 'borrador' => 'secondary'
];
$tiene_cert = !empty($config['cert_file']) && !empty($config['cert_pass']) && file_exists(FCPATH . $config['cert_file']);
?>
<div class="content-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="card-title mb-0"><i class="fa fa-flask text-primary"></i> Pruebas de Servicios Web SIFEN</h4>
                <small class="text-muted">Envío, consultas y eventos contra
                    <code><?= $config['ambiente'] === 'prod' ? 'sifen.set.gov.py' : 'sifen-test.set.gov.py' ?></code></small>
            </div>
            <div class="mt-1 mt-md-0">
                <span class="badge badge-<?= $config['ambiente'] === 'prod' ? 'danger' : 'info' ?> p-1">
                    <?= $config['ambiente'] === 'prod' ? 'PRODUCCIÓN' : 'TEST' ?>
                </span>
                <span class="badge badge-<?= $tiene_cert ? 'success' : 'warning' ?> p-1">
                    <?= $tiene_cert ? 'Certificado cargado' : 'Sin certificado' ?>
                </span>
                <a href="<?= base_url('sifen_config') ?>" class="btn btn-sm btn-outline-secondary ml-1"><i class="fa fa-cog"></i> Configuración</a>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">

                <?php if (!$tiene_cert) { ?>
                    <div class="alert alert-warning">
                        <strong>Falta el certificado digital.</strong> SIFEN exige autenticación mutua: sin un certificado
                        cualificado (.p12 con tu RUC) ninguna operación llega al servidor. Cargalo en
                        <a href="<?= base_url('sifen_config') ?>">Configuración</a>.
                    </div>
                <?php } ?>

                <div id="resultado" class="mb-2" style="display:none;"></div>

                <!-- Avance de la Guía de Pruebas -->
                <h5 class="text-primary font-weight-bold"><i class="fa fa-list-ol"></i> Avance de la Guía de Pruebas (Factura Electrónica)</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered mb-0">
                        <thead><tr><th>Prueba mínima</th><th class="text-center">Realizadas</th><th class="text-center">Mínimo</th></tr></thead>
                        <tbody>
                        <?php foreach ($avance as $a) { $ok = $a[1] >= $a[2]; ?>
                            <tr>
                                <td><?= $h($a[0]) ?></td>
                                <td class="text-center font-weight-bold text-<?= $ok ? 'success' : 'muted' ?>"><?= (int)$a[1] ?></td>
                                <td class="text-center"><?= (int)$a[2] ?> <?= $ok ? '<i class="fa fa-check text-success"></i>' : '' ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Facturas -->
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                    <h5 class="text-primary font-weight-bold mb-0"><i class="fa fa-file-text-o"></i> Facturas electrónicas</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="enviarLote()">
                        <i class="fa fa-cubes"></i> Enviar seleccionadas como lote
                    </button>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                        <tr>
                            <th style="width:30px;"><input type="checkbox" onclick="document.querySelectorAll('.chk-fac').forEach(c => c.checked = this.checked)"></th>
                            <th>N° SIFEN</th>
                            <th>Fecha</th>
                            <th class="text-right">Total</th>
                            <th>Estado</th>
                            <th>CDC</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!$facturas) { ?>
                            <tr><td colspan="7" class="text-center text-muted">No hay facturas procesadas para SIFEN.</td></tr>
                        <?php } ?>
                        <?php foreach ($facturas as $f) { $est = $f['sifen_estado'] ?: 'borrador'; ?>
                            <tr>
                                <td><input type="checkbox" class="chk-fac" value="<?= (int)$f['id'] ?>"></td>
                                <td><?= $h($f['sifen_numero']) ?></td>
                                <td><?= $h($f['invoicedate']) ?></td>
                                <td class="text-right"><?= number_format((float)$f['total'], 0, ',', '.') ?></td>
                                <td><span class="badge badge-<?= $badge_estado[$est] ?? 'secondary' ?>"><?= $h($est) ?></span></td>
                                <td><code class="font-small-2"><?= $h($f['cdc']) ?></code></td>
                                <td class="text-right text-nowrap">
                                    <button class="btn btn-sm btn-success" title="Enviar (WS síncrono)" onclick="op('enviar', {id: <?= (int)$f['id'] ?>})"><i class="fa fa-paper-plane"></i></button>
                                    <button class="btn btn-sm btn-info" title="Consultar por CDC" onclick="op('consultar', {cdc: '<?= $h($f['cdc']) ?>'})"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Regenerar y firmar XML" onclick="op('regenerar', {id: <?= (int)$f['id'] ?>})"><i class="fa fa-refresh"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" title="Cancelar (evento)" onclick="cancelar('<?= $h($f['cdc']) ?>')"><i class="fa fa-ban"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Herramientas -->
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="card border h-100 mb-0"><div class="card-body p-2">
                            <h6 class="font-weight-bold"><i class="fa fa-id-card-o"></i> Consulta RUC</h6>
                            <div class="input-group input-group-sm">
                                <input type="text" id="ruc_consulta" class="form-control" placeholder="RUC sin DV (ej. 80012345)">
                                <div class="input-group-append"><button class="btn btn-primary" onclick="op('ruc', {ruc: val('ruc_consulta')})">Consultar</button></div>
                            </div>
                        </div></div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card border h-100 mb-0"><div class="card-body p-2">
                            <h6 class="font-weight-bold"><i class="fa fa-cubes"></i> Resultado de lote</h6>
                            <div class="input-group input-group-sm">
                                <input type="text" id="lote_consulta" class="form-control" placeholder="N° de lote (dProtConsLote)">
                                <div class="input-group-append"><button class="btn btn-primary" onclick="op('consultar_lote', {lote: val('lote_consulta')})">Consultar</button></div>
                            </div>
                        </div></div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card border h-100 mb-0"><div class="card-body p-2">
                            <h6 class="font-weight-bold"><i class="fa fa-eraser"></i> Inutilizar numeración</h6>
                            <div class="form-row">
                                <div class="col-6 mb-1"><input type="number" id="inu_desde" class="form-control form-control-sm" placeholder="Desde N°" min="1"></div>
                                <div class="col-6 mb-1"><input type="number" id="inu_hasta" class="form-control form-control-sm" placeholder="Hasta N°" min="1"></div>
                                <div class="col-12 mb-1">
                                    <select id="inu_tipo" class="form-control form-control-sm">
                                        <option value="1">Factura electrónica</option>
                                        <option value="5">Nota de crédito electrónica</option>
                                        <option value="6">Nota de débito electrónica</option>
                                        <option value="4">Autofactura electrónica</option>
                                        <option value="7">Nota de remisión electrónica</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-1"><input type="text" id="inu_motivo" class="form-control form-control-sm" placeholder="Motivo (mín. 5 caracteres)"></div>
                                <div class="col-12"><button class="btn btn-sm btn-warning btn-block" onclick="inutilizar()">Inutilizar rango</button></div>
                            </div>
                            <small class="text-muted">Timbrado <?= $h($config['timbrado']) ?>, est. <?= $h($config['establecimiento']) ?>, punto <?= $h($config['punto_expedicion']) ?>.</small>
                        </div></div>
                    </div>
                </div>

                <!-- Registro -->
                <h5 class="text-primary font-weight-bold mt-2"><i class="fa fa-history"></i> Registro de operaciones</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 font-small-3">
                        <thead>
                        <tr><th>dId</th><th>Fecha</th><th>Operación</th><th>Referencia</th><th>Estado</th><th>Código / mensaje</th><th>Protocolo</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php if (!$log) { ?>
                            <tr><td colspan="8" class="text-center text-muted">Todavía no se realizaron operaciones.</td></tr>
                        <?php } ?>
                        <?php foreach ($log as $l) { ?>
                            <tr>
                                <td><?= (int)$l['id'] ?></td>
                                <td class="text-nowrap"><?= $h($l['created_at']) ?></td>
                                <td><?= $h($l['operacion']) ?> <?= $l['ambiente'] === 'prod' ? '<span class="badge badge-danger">prod</span>' : '' ?></td>
                                <td><code class="font-small-2"><?= $h($l['referencia'] ?: $l['cdc']) ?></code></td>
                                <td><?= $h($l['estado']) ?></td>
                                <td><?= $h(trim($l['codigo'] . ' ' . $l['mensaje'])) ?></td>
                                <td><?= $h($l['protocolo']) ?></td>
                                <td><button class="btn btn-sm btn-outline-secondary" title="Ver XML enviado y recibido" onclick="verXml(<?= (int)$l['id'] ?>)"><i class="fa fa-code"></i></button></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal XML -->
<div class="modal fade" id="modalXml" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mensajes SOAP <span id="xml_titulo"></span></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <h6>Request</h6>
                <pre id="xml_req" style="max-height:260px; overflow:auto; white-space:pre-wrap; word-break:break-all; background:#f6f8fc; padding:8px; font-size:11px;"></pre>
                <h6 class="mt-1">Response</h6>
                <pre id="xml_res" style="max-height:260px; overflow:auto; white-space:pre-wrap; word-break:break-all; background:#f6f8fc; padding:8px; font-size:11px;"></pre>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var urlOp = '<?= base_url('sifen_config/operacion') ?>';

    function val(id) { return document.getElementById(id).value.trim(); }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    function mostrar(tipo, html) {
        var box = document.getElementById('resultado');
        box.style.display = 'block';
        box.innerHTML = '<div class="alert alert-' + tipo + ' mb-0">' + html + '</div>';
        box.scrollIntoView({behavior: 'smooth', block: 'start'});
    }

    function resumen(r) {
        var partes = [];
        if (r.estado) partes.push('<strong>Estado:</strong> ' + esc(r.estado));
        if (r.codigo) partes.push('<strong>Código:</strong> ' + esc(r.codigo) + ' ' + esc(r.mensaje || ''));
        if (r.protocolo) partes.push('<strong>Protocolo:</strong> ' + esc(r.protocolo));
        if (r.lote) partes.push('<strong>N° de lote:</strong> ' + esc(r.lote) + (r.tiempo ? ' (proceso estimado ' + esc(r.tiempo) + ' s)' : ''));
        if (r.razon_social) partes.push('<strong>Razón social:</strong> ' + esc(r.razon_social) + ' — ' + esc(r.estado || '') + (r.facturador ? ' — facturador electrónico' : ''));
        (r.mensajes || []).forEach(function (m) { partes.push(esc(m.codigo) + ' - ' + esc(m.mensaje)); });
        (r.resultados || []).forEach(function (x) {
            partes.push('<code>' + esc(x.cdc) + '</code>: ' + esc(x.estado) + ' ' + (x.mensajes || []).map(function (m) { return esc(m.codigo + ' - ' + m.mensaje); }).join('; '));
        });
        if (r.firmado !== undefined) partes.push(r.firmado ? 'XML regenerado y firmado.' : 'XML regenerado SIN firma (falta certificado).');
        if (r.log_id) partes.push('<small class="text-muted">dId / registro #' + esc(r.log_id) + '</small>');
        return partes.join('<br>');
    }

    function op(accion, datos) {
        var fd = new FormData();
        fd.append('op', accion);
        if (typeof crsf_token !== 'undefined') fd.append(crsf_token, crsf_hash);
        Object.keys(datos).forEach(function (k) {
            var v = datos[k];
            if (Array.isArray(v)) v.forEach(function (x) { fd.append(k + '[]', x); });
            else fd.append(k, v);
        });
        mostrar('secondary', '<i class="fa fa-spinner fa-spin"></i> Procesando <strong>' + esc(accion) + '</strong>...');
        fetch(urlOp, {method: 'POST', body: fd, credentials: 'same-origin'})
            .then(function (res) { return res.json(); })
            .then(function (r) {
                if (!r.status) {
                    mostrar('danger', '<strong>Error:</strong> ' + esc(r.message) + (r.log_id ? '<br><small>Registro #' + esc(r.log_id) + '</small>' : ''));
                    return;
                }
                var ok = r.aprobado || r.encontrado || r.recibido || r.concluido || accion === 'regenerar';
                mostrar(ok ? 'success' : 'warning', resumen(r) + '<br><a href="javascript:location.reload()">Actualizar listado</a>');
            })
            .catch(function (e) { mostrar('danger', 'Error de comunicación: ' + esc(e)); });
    }

    function enviarLote() {
        var ids = Array.prototype.map.call(document.querySelectorAll('.chk-fac:checked'), function (c) { return c.value; });
        if (!ids.length) { mostrar('warning', 'Seleccioná al menos una factura.'); return; }
        if (ids.length > 50) { mostrar('warning', 'Un lote admite como máximo 50 facturas.'); return; }
        op('lote', {ids: ids});
    }

    function cancelar(cdc) {
        var motivo = prompt('Motivo de la cancelación (5 a 500 caracteres):');
        if (motivo === null) return;
        op('cancelar', {cdc: cdc, motivo: motivo});
    }

    function inutilizar() {
        if (!confirm('¿Inutilizar el rango ' + val('inu_desde') + ' a ' + val('inu_hasta') + '? Esos números no podrán volver a usarse.')) return;
        op('inutilizar', {desde: val('inu_desde'), hasta: val('inu_hasta'), tipo: val('inu_tipo'), motivo: val('inu_motivo')});
    }

    function verXml(id) {
        document.getElementById('xml_titulo').textContent = '#' + id;
        document.getElementById('xml_req').textContent = 'Cargando...';
        document.getElementById('xml_res').textContent = '';
        $('#modalXml').modal('show');
        fetch('<?= base_url('sifen_config/log_detalle') ?>/' + id, {credentials: 'same-origin'})
            .then(function (res) { return res.json(); })
            .then(function (r) {
                document.getElementById('xml_req').textContent = r.request || '(sin request)';
                document.getElementById('xml_res').textContent = r.response || '(sin response)';
            });
    }
</script>
