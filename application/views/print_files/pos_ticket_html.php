<?php
/**
 * GeeksPOS — Plantilla de Ticket Térmico Universal (80mm / 58mm)
 * Compatible con KuDE / Facturación Electrónica SIFEN (DNIT Paraguay v150)
 * Compatible con impresoras térmicas ESC/POS (USB, Bluetooth, Red) en PC y móvil
 */
$loc = location($invoice['loc']);
$ticket_width = (isset($width) && $width == '58') ? 58 : 80;
$print_width_px = ($ticket_width == 58) ? 210 : 300;

// Resolver Logo de la Empresa (Base64 Data URI para impresión inmediata sin fallos de red)
$CI =& get_instance();
$logo_data_uri = '';
$candidate_logos = [];

// 1. Logo de la sucursal/ubicación si existe
if (!empty($loc['logo'])) {
    $candidate_logos[] = FCPATH . 'userfiles/company/' . $loc['logo'];
}

// 2. Logo principal en la tabla geopos_system
$sys_row = $CI->db->select('logo')->where('id', 1)->get('geopos_system')->row_array();
if (!empty($sys_row['logo'])) {
    $candidate_logos[] = FCPATH . 'userfiles/company/' . $sys_row['logo'];
}

// 3. Fallbacks estándar en disco
$candidate_logos[] = FCPATH . 'userfiles/company/logo.png';
$candidate_logos[] = FCPATH . 'userfiles/company/default.png';

foreach ($candidate_logos as $cand_path) {
    if (!empty($cand_path) && file_exists($cand_path) && is_file($cand_path) && filesize($cand_path) > 50) {
        $raw_img = @file_get_contents($cand_path);
        if ($raw_img) {
            $ext = strtolower(pathinfo($cand_path, PATHINFO_EXTENSION));
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : (($ext === 'gif') ? 'image/gif' : 'image/png');
            $logo_data_uri = 'data:' . $mime . ';base64,' . base64_encode($raw_img);
            break;
        }
    }
}

// Verificar si la factura tiene datos de Factura Electrónica SIFEN
$is_sifen = !empty($invoice['cdc']);
$sifen_config = null;
$qr_data_uri = '';
$formatted_cdc = '';

if ($is_sifen) {
    $CI =& get_instance();
    if (!isset($CI->sifen)) {
        $CI->load->library('sifen');
    }
    $sifen_config = $CI->sifen->get_config();
    $formatted_cdc = $CI->sifen->formatear_cdc($invoice['cdc']);
    if (!empty($invoice['sifen_qr'])) {
        $qr_data_uri = $CI->sifen->generar_qr_data_uri($invoice['sifen_qr'], ($ticket_width == 58 ? 130 : 160));
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_sifen ? ('KuDE ' . $invoice['sifen_numero']) : ($this->config->item('prefix') . ' #' . $invoice['tid']) ?> — Ticket</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: <?= ($ticket_width == 58) ? '12px' : '13px' ?>;
            color: #000;
            line-height: 1.3;
        }
        .ticket-wrapper {
            width: <?= $ticket_width ?>mm;
            max-width: 100%;
            margin: 15px auto;
            background: #fff;
            padding: <?= ($ticket_width == 58) ? '3mm' : '5mm' ?>;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .upper { text-transform: uppercase; }

        .logo-img {
            max-height: <?= ($ticket_width == 58) ? '75px' : '95px' ?>;
            max-width: <?= ($ticket_width == 58) ? '90%' : '85%' ?>;
            margin: 2px auto 8px auto;
            display: block;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
        }

        .title {
            font-size: <?= ($ticket_width == 58) ? '14px' : '17px' ?>;
            font-weight: 800;
            margin: 2px 0;
        }

        /* Badge de Pedido Destacado (Grande para Cocina y Despacho) */
        .badge-order {
            border: 2.5px solid #000;
            padding: 6px 8px;
            margin: 8px 0;
            text-align: center;
            background: #fff;
        }
        .badge-order .order-type-tag {
            display: block;
            font-size: <?= ($ticket_width == 58) ? '13px' : '15px' ?>;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-order .order-name-text {
            display: block;
            font-size: <?= ($ticket_width == 58) ? '17px' : '22px' ?>;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .divider-double {
            border: none;
            border-top: 2px solid #000;
            margin: 7px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            font-size: <?= ($ticket_width == 58) ? '11px' : '12.5px' ?>;
            vertical-align: top;
        }

        /* Tabla de Productos del Pedido (Más grande y legible) */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }
        .items-table th {
            border-bottom: 2px solid #000;
            padding: 4px 0;
            font-size: <?= ($ticket_width == 58) ? '11px' : '13px' ?>;
            font-weight: 800;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 1px dotted #e0e0e0;
        }
        .item-qty {
            font-size: <?= ($ticket_width == 58) ? '15px' : '17px' ?>;
            font-weight: 900;
            color: #000;
            width: 13%;
            vertical-align: top;
            padding-top: 4px;
        }
        .item-name {
            font-size: <?= ($ticket_width == 58) ? '14px' : '16px' ?>;
            font-weight: 700;
            line-height: 1.25;
            color: #000;
        }
        .item-unit-price {
            font-size: <?= ($ticket_width == 58) ? '10.5px' : '12px' ?>;
            color: #333;
            display: block;
            margin-top: 2px;
        }
        .item-tax {
            font-size: <?= ($ticket_width == 58) ? '10.5px' : '11.5px' ?>;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .item-subtotal {
            font-size: <?= ($ticket_width == 58) ? '14px' : '16px' ?>;
            font-weight: 800;
            white-space: nowrap;
            text-align: right;
            vertical-align: top;
            padding-top: 4px;
        }

        .total-row td {
            font-size: <?= ($ticket_width == 58) ? '16px' : '19px' ?> !important;
            font-weight: 900 !important;
            padding-top: 6px;
        }

        /* Barra de control en pantalla (oculta al imprimir) */
        .no-print-bar {
            position: sticky;
            top: 0;
            background: #0b2545;
            color: #fff;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 999;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover { background: #059669; }
        .btn-secondary {
            background: rgba(255,255,255,0.18);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.28); color: #fff; }

        @media print {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .ticket-wrapper {
                width: <?= $ticket_width ?>mm !important;
                margin: 0 !important;
                padding: <?= ($ticket_width == 58) ? '1mm' : '2mm' ?> !important;
                box-shadow: none !important;
            }
            .no-print, .no-print-bar {
                display: none !important;
            }
            @page {
                size: <?= $ticket_width ?>mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar no-print">
    <div style="font-size: 14px; font-weight: 600;">
        <?= $is_sifen ? ('KuDE Factura ' . $invoice['sifen_numero']) : ('Ticket #' . $invoice['tid']) ?> &nbsp;•&nbsp;
        <span style="opacity: .8; font-size: 12px;"><?= $ticket_width ?>mm</span>
        <?php if ($is_sifen) { ?>
            <span style="background: #10b981; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin-left: 6px;">SIFEN DTE</span>
        <?php } ?>
    </div>
    <div style="display:flex; gap: 8px; align-items:center;">
        <a href="?id=<?= $invoice['iid'] ?>&width=<?= ($ticket_width == 80) ? '58' : '80' ?>&print=0" class="btn-secondary" title="Cambiar ancho">
            <?= ($ticket_width == 80) ? '58mm' : '80mm' ?>
        </a>
        <?php if ($is_sifen && !empty($invoice['sifen_xml']) && file_exists(FCPATH . $invoice['sifen_xml'])) { 
            $token_q = !empty($this->input->get('token')) ? ('&token=' . $this->input->get('token')) : '';
        ?>
            <a href="<?= base_url('pos_invoices/download_dte?id=' . $invoice['iid'] . $token_q) ?>" class="btn-secondary" title="Descargar XML firmado">
                📥 XML DTE
            </a>
        <?php } ?>
        <button onclick="window.print()" class="btn-print">
            🖨️ Imprimir Ticket
        </button>
        <button onclick="window.close()" class="btn-secondary">
            Cerrar
        </button>
    </div>
</div>

<div class="ticket-wrapper">

    <?php if ($is_sifen) { ?>
        <!-- ============================================== -->
        <!-- ENCABEZADO OFICIAL KuDE - SIFEN PARAGUAY       -->
        <!-- ============================================== -->
        <div class="text-center">
            <?php if (!empty($logo_data_uri)) { ?>
                <img class="logo-img" src="<?= $logo_data_uri ?>" alt="Logo">
            <?php } elseif (!empty($loc['logo'])) { ?>
                <img class="logo-img" src="<?= base_url('userfiles/company/' . $loc['logo']) ?>" alt="Logo">
            <?php } ?>
            <div class="title upper"><?= !empty($sifen_config['razon_social']) ? htmlspecialchars($sifen_config['razon_social']) : (!empty($loc['cname']) ? $loc['cname'] : $this->config->item('ctitle')) ?></div>
            <?php if (!empty($sifen_config['nombre_fantasia']) && $sifen_config['nombre_fantasia'] !== $sifen_config['razon_social']) { ?>
                <div style="font-size: 11px;"><?= htmlspecialchars($sifen_config['nombre_fantasia']) ?></div>
            <?php } ?>
            <div class="bold" style="font-size: 12px; margin: 2px 0;">RUC: <?= $sifen_config['ruc'] . '-' . $sifen_config['dv'] ?></div>
            <?php if (!empty($sifen_config['actividad_descripcion'])) { ?>
                <div style="font-size: 10px;"><?= htmlspecialchars($sifen_config['actividad_descripcion']) ?></div>
            <?php } ?>
            <?php if (!empty($sifen_config['direccion'])) { ?>
                <div style="font-size: 10px;"><?= htmlspecialchars($sifen_config['direccion']) ?></div>
            <?php } ?>
            <?php if (!empty($sifen_config['telefono'])) { ?>
                <div style="font-size: 10px;">Tel: <?= htmlspecialchars($sifen_config['telefono']) ?></div>
            <?php } ?>

            <hr class="divider">

            <div class="bold" style="font-size: 11px; letter-spacing: 0.5px;">DOCUMENTO TRIBUTARIO ELECTRÓNICO</div>
            <div class="bold" style="font-size: 12px;">KuDE DE FACTURA ELECTRÓNICA</div>
            <div style="font-size: 10px;">Timbrado Nº: <strong><?= htmlspecialchars($sifen_config['timbrado']) ?></strong></div>
            <div style="font-size: 10px;">Inicio Vigencia: <?= date('d/m/Y', strtotime($sifen_config['timbrado_fecha'])) ?></div>
            <div class="bold" style="font-size: 12px; margin: 3px 0; border: 1px dashed #000; padding: 2px;">
                FACTURA Nº <?= !empty($invoice['sifen_numero']) ? $invoice['sifen_numero'] : sprintf('%03d-%03d-%07d', $sifen_config['establecimiento'], $sifen_config['punto_expedicion'], $invoice['tid']) ?>
            </div>
        </div>

    <?php } else { ?>
        <!-- ============================================== -->
        <!-- ENCABEZADO COMERCIAL ESTÁNDAR                  -->
        <!-- ============================================== -->
        <div class="text-center">
            <?php if (!empty($logo_data_uri)) { ?>
                <img class="logo-img" src="<?= $logo_data_uri ?>" alt="Logo">
            <?php } elseif (!empty($loc['logo'])) { ?>
                <img class="logo-img" src="<?= base_url('userfiles/company/' . $loc['logo']) ?>" alt="Logo">
            <?php } ?>
            <div class="title upper"><?= !empty($loc['cname']) ? $loc['cname'] : $this->config->item('ctitle') ?></div>
            <?php if (!empty($loc['taxid'])) { ?>
                <div>RUC / Tax ID: <?= $loc['taxid'] ?></div>
            <?php } ?>
            <?php if (!empty($loc['address'])) { ?>
                <div><?= $loc['address'] ?><?= !empty($loc['city']) ? ' - ' . $loc['city'] : '' ?></div>
            <?php } ?>
            <?php if (!empty($loc['phone'])) { ?>
                <div>Tel: <?= $loc['phone'] ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- Badge de Tipo de Pedido / Mesa / Cliente del Pedido (Destacado) -->
    <?php if (!empty($invoice['order_type']) || !empty($invoice['order_name'])) { ?>
        <div class="badge-order">
            <?php if (!empty($invoice['order_type'])) { ?>
                <span class="order-type-tag">[ <?= htmlspecialchars($invoice['order_type']) ?> ]</span>
            <?php } ?>
            <?php if (!empty($invoice['order_name'])) { ?>
                <span class="order-name-text"><?= htmlspecialchars($invoice['order_name']) ?></span>
            <?php } ?>
        </div>
    <?php } else { ?>
        <hr class="divider">
    <?php } ?>

    <!-- Metadatos de la Venta / Cliente -->
    <table class="meta-table">
        <?php if (!$is_sifen) { ?>
        <tr>
            <td class="bold">Ticket Nº:</td>
            <td class="text-right"><?= $this->config->item('prefix') . ' #' . $invoice['tid'] ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="bold">Fecha y Hora:</td>
            <td class="text-right"><?= dateformat($invoice['invoicedate']) ?> <?= !empty($invoice['r_time']) ? $invoice['r_time'] : date('H:i') ?></td>
        </tr>
        <tr>
            <td class="bold">Condición:</td>
            <td class="text-right upper"><?= ($invoice['status'] === 'paid') ? 'Contado' : 'Crédito' ?></td>
        </tr>
        <?php if ($is_sifen) { ?>
        <tr>
            <td class="bold">RUC / CI:</td>
            <td class="text-right bold"><?= !empty($invoice['taxid']) ? $invoice['taxid'] : 'Sin Nombre / Consumidor Final' ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Cliente:</td>
            <td class="text-right bold"><?= !empty($invoice['name']) ? $invoice['name'] : 'Consumidor Final' ?></td>
        </tr>
        <?php if (!empty($employee['name'])) { ?>
        <tr>
            <td>Cajero/a:</td>
            <td class="text-right"><?= $employee['name'] ?></td>
        </tr>
        <?php } ?>
        <?php if (!empty($invoice['card_txn'])) { ?>
        <tr>
            <td>Nº Transacción:</td>
            <td class="text-right bold"><?= $invoice['card_txn'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <hr class="divider">

    <!-- Detalle de Productos del Pedido (Formato Grande y Legible) -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 13%;">Cant</th>
                <th class="text-left" style="width: <?= $is_sifen ? '51%' : '57%' ?>;">Producto</th>
                <?php if ($is_sifen) { ?>
                <th class="text-center" style="width: 12%;">IVA</th>
                <?php } ?>
                <th class="text-right" style="width: <?= $is_sifen ? '24%' : '30%' ?>;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $row) { 
                $tax_label = '10%';
                if (isset($row['tax'])) {
                    if ((float)$row['tax'] == 5) {
                        $tax_label = '5%';
                    } elseif ((float)$row['tax'] == 0 && isset($row['taxstatus']) && $row['taxstatus'] == 'no') {
                        $tax_label = 'EX';
                    }
                }
            ?>
                <tr>
                    <td class="text-left item-qty"><?= +$row['qty'] ?></td>
                    <td class="text-left">
                        <div class="item-name"><?= $row['product'] ?></div>
                        <?php if ($row['qty'] > 1) { ?>
                            <span class="item-unit-price">@ <?= amountExchange($row['price'], $invoice['multi'], $invoice['loc']) ?> c/u</span>
                        <?php } ?>
                    </td>
                    <?php if ($is_sifen) { ?>
                    <td class="item-tax"><?= $tax_label ?></td>
                    <?php } ?>
                    <td class="item-subtotal"><?= amountExchange($row['subtotal'], $invoice['multi'], $invoice['loc']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <hr class="divider">

    <!-- Totales -->
    <table class="meta-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right"><?= amountExchange($invoice['subtotal'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php if ($invoice['discount'] > 0) { ?>
        <tr>
            <td>Descuento:</td>
            <td class="text-right">- <?= amountExchange($invoice['discount'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <?php if (!empty($invoice['shipping']) && $invoice['shipping'] > 0) { ?>
        <tr>
            <td>Envío / Delivery:</td>
            <td class="text-right"><?= amountExchange($invoice['shipping'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <tr class="total-row">
            <td class="bold">TOTAL A PAGAR:</td>
            <td class="text-right bold"><?= amountExchange($invoice['total'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
    </table>

    <?php if ($is_sifen) { 
        $iva10 = isset($invoice['iva_10']) ? (float)$invoice['iva_10'] : round($invoice['total'] / 11);
        $iva5 = isset($invoice['iva_5']) ? (float)$invoice['iva_5'] : 0.0;
        $iva_tot = $iva10 + $iva5;
        $grav10 = isset($invoice['gravada_10']) ? (float)$invoice['gravada_10'] : ($invoice['total'] - $iva10);
        $grav5 = isset($invoice['gravada_5']) ? (float)$invoice['gravada_5'] : 0.0;
        $exenta = isset($invoice['iva_exenta']) ? (float)$invoice['iva_exenta'] : 0.0;
    ?>
    <hr class="divider">

    <!-- Desglose de Liquidación del IVA (Obligatorio DNIT / SIFEN) -->
    <div class="bold upper" style="font-size: 10px; margin-bottom: 2px;">Liquidación del IVA (IVA Incluido)</div>
    <table class="meta-table" style="font-size: 10px;">
        <tr>
            <td>Gravadas 10%:</td>
            <td class="text-right"><?= amountExchange($grav10, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php if ($grav5 > 0) { ?>
        <tr>
            <td>Gravadas 5%:</td>
            <td class="text-right"><?= amountExchange($grav5, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <?php if ($exenta > 0) { ?>
        <tr>
            <td>Exentas:</td>
            <td class="text-right"><?= amountExchange($exenta, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="bold">Liquidación IVA 10%:</td>
            <td class="text-right bold"><?= amountExchange($iva10, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php if ($iva5 > 0) { ?>
        <tr>
            <td class="bold">Liquidación IVA 5%:</td>
            <td class="text-right bold"><?= amountExchange($iva5, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <tr style="border-top: 1px dotted #000;">
            <td class="bold">TOTAL IVA:</td>
            <td class="text-right bold"><?= amountExchange($iva_tot, $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
    </table>
    <?php } ?>

    <hr class="divider">

    <!-- Estado y Medio de Pago -->
    <table class="meta-table">
        <tr>
            <td>Estado:</td>
            <td class="text-right bold upper"><?= $this->lang->line(ucwords($invoice['status'])) ?: $invoice['status'] ?></td>
        </tr>
        <?php if (!empty($invoice['pmethod'])) { ?>
        <tr>
            <td>Medio de Pago:</td>
            <td class="text-right bold"><?= $invoice['pmethod'] ?></td>
        </tr>
        <?php } ?>
        <?php if (!empty($invoice['pamnt']) && $invoice['pamnt'] > 0) { ?>
        <tr>
            <td>Importe Pagado:</td>
            <td class="text-right"><?= amountExchange($invoice['pamnt'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
    </table>

    <?php if ($is_sifen && !empty($invoice['cdc'])) { ?>
    <hr class="divider-double">

    <!-- CDC (Código de Control de 44 dígitos) -->
    <div class="text-center" style="margin: 6px 0;">
        <div class="bold" style="font-size: 10px;">CÓDIGO DE CONTROL (CDC):</div>
        <div style="font-family: monospace; font-size: 10px; font-weight: bold; letter-spacing: 0.5px; word-break: break-all; margin: 3px 0; background: #fafafa; padding: 3px; border: 1px dotted #888;">
            <?= $formatted_cdc ?>
        </div>
    </div>

    <!-- Código QR Oficial KuDE -->
    <?php if (!empty($qr_data_uri)) { ?>
    <div class="text-center" style="margin: 6px 0;">
        <img src="<?= $qr_data_uri ?>" alt="QR KuDE" style="width: <?= ($ticket_width == 58) ? '125px' : '155px' ?>; height: <?= ($ticket_width == 58) ? '125px' : '155px' ?>; display: inline-block;">
    </div>
    <?php } ?>

    <div class="text-center" style="font-size: 9px; line-height: 1.25; margin-top: 2px;">
        Consulte la validez de esta Factura Electrónica escaneando el código QR o en:<br>
        <span style="word-break: break-all; font-weight: bold;">https://sifen.dnit.gov.py</span>
    </div>
    <?php } else { ?>
    <hr class="divider-double">
    <!-- Mensaje de Cierre Estándar -->
    <div class="text-center" style="margin-top: 8px;">
        <div class="bold"><?= $this->lang->line('Thank you') ?></div>
        <small style="font-size: 10px; opacity: .85;">Conserve este comprobante como constancia.</small>
    </div>
    <?php } ?>

</div>

<?php if (!empty($autoprint)) { ?>
<script type="text/javascript">
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 300);
    });
    window.onafterprint = function() {
        if (window.opener) {
            setTimeout(function() { window.close(); }, 600);
        }
    };
</script>
<?php } ?>

</body>
</html>
