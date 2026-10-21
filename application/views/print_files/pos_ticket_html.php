<?php
/**
 * GeeksPOS — Plantilla de Ticket Térmico Universal (80mm / 58mm)
 * Compatible con navegadores en PC, tablets y smartphones (Bluetooth / USB / Red)
 */
$loc = location($invoice['loc']);
$ticket_width = (isset($width) && $width == '58') ? 58 : 80;
$print_width_px = ($ticket_width == 58) ? 210 : 300;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->config->item('prefix') . ' #' . $invoice['tid'] ?> — Ticket</title>
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
            font-family: 'Courier New', Courier, monospace, system-ui;
            font-size: <?= ($ticket_width == 58) ? '11px' : '12px' ?>;
            color: #000;
            line-height: 1.25;
        }
        .ticket-wrapper {
            width: <?= $ticket_width ?>mm;
            max-width: 100%;
            margin: 15px auto;
            background: #fff;
            padding: <?= ($ticket_width == 58) ? '3mm' : '4mm' ?>;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .upper { text-transform: uppercase; }

        .logo-img {
            max-height: 55px;
            max-width: 85%;
            filter: grayscale(100%);
            margin: 2px auto 6px auto;
            display: block;
        }

        .title {
            font-size: <?= ($ticket_width == 58) ? '14px' : '16px' ?>;
            font-weight: bold;
            margin: 2px 0;
        }

        .badge-order {
            border: 2px solid #000;
            padding: 4px 6px;
            margin: 6px 0;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .divider-double {
            border: none;
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 1px 0;
            font-size: <?= ($ticket_width == 58) ? '10px' : '11px' ?>;
            vertical-align: top;
        }

        .items-table th {
            border-bottom: 1px solid #000;
            padding: 3px 0;
            font-size: <?= ($ticket_width == 58) ? '10px' : '11px' ?>;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .total-row {
            font-size: <?= ($ticket_width == 58) ? '14px' : '17px' ?>;
            font-weight: bold;
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
        Ticket #<?= $invoice['tid'] ?> &nbsp;•&nbsp;
        <span style="opacity: .8; font-size: 12px;"><?= $ticket_width ?>mm</span>
    </div>
    <div style="display:flex; gap: 8px; align-items:center;">
        <a href="?id=<?= $invoice['iid'] ?>&width=<?= ($ticket_width == 80) ? '58' : '80' ?>&print=0" class="btn-secondary" title="Cambiar ancho">
            Cambiar a <?= ($ticket_width == 80) ? '58mm' : '80mm' ?>
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨️ Imprimir Ticket
        </button>
        <button onclick="window.close()" class="btn-secondary">
            Cerrar
        </button>
    </div>
</div>

<div class="ticket-wrapper">
    <!-- Encabezado de la Empresa -->
    <div class="text-center">
        <?php if (!empty($loc['logo'])) { ?>
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

    <!-- Badge de Tipo de Pedido / Mesa -->
    <?php if (!empty($invoice['order_type']) || !empty($invoice['order_name'])) { ?>
        <div class="badge-order upper">
            <?php if (!empty($invoice['order_type'])) { ?>
                <span>[ <?= $invoice['order_type'] ?> ]</span>
            <?php } ?>
            <?php if (!empty($invoice['order_name'])) { ?>
                <div><?= $invoice['order_name'] ?></div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <hr class="divider">
    <?php } ?>

    <!-- Metadatos de la Venta -->
    <table class="meta-table">
        <tr>
            <td class="bold">Ticket Nº:</td>
            <td class="text-right"><?= $this->config->item('prefix') . ' #' . $invoice['tid'] ?></td>
        </tr>
        <tr>
            <td class="bold">Fecha:</td>
            <td class="text-right"><?= dateformat($invoice['invoicedate']) ?> <?= date('H:i') ?></td>
        </tr>
        <?php if (!empty($employee['name'])) { ?>
        <tr>
            <td>Cajero/a:</td>
            <td class="text-right"><?= $employee['name'] ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Cliente:</td>
            <td class="text-right bold"><?= !empty($invoice['name']) ? $invoice['name'] : 'Consumidor Final' ?></td>
        </tr>
        <?php if (!empty($invoice['card_txn'])) { ?>
        <tr>
            <td>Nº Transacción:</td>
            <td class="text-right bold"><?= $invoice['card_txn'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <hr class="divider">

    <!-- Detalle de Productos -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 15%;">Cant</th>
                <th class="text-left" style="width: 55%;">Producto</th>
                <th class="text-right" style="width: 30%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $row) { ?>
                <tr>
                    <td class="text-left bold"><?= +$row['qty'] ?></td>
                    <td class="text-left">
                        <div><?= $row['product'] ?></div>
                        <?php if ($row['qty'] > 1) { ?>
                            <small style="opacity: .8;">@ <?= amountExchange($row['price'], $invoice['multi'], $invoice['loc']) ?></small>
                        <?php } ?>
                    </td>
                    <td class="text-right"><?= amountExchange($row['subtotal'], $invoice['multi'], $invoice['loc']) ?></td>
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
        <?php if ($invoice['tax'] > 0) { ?>
        <tr>
            <td>Impuesto / IVA:</td>
            <td class="text-right"><?= amountExchange($invoice['tax'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
        <?php } ?>
        <tr class="total-row">
            <td class="bold">TOTAL:</td>
            <td class="text-right bold"><?= amountExchange($invoice['total'], $invoice['multi'], $invoice['loc']) ?></td>
        </tr>
    </table>

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

    <hr class="divider-double">

    <!-- Mensaje de Cierre / Despedida -->
    <div class="text-center" style="margin-top: 8px;">
        <div class="bold"><?= $this->lang->line('Thank you') ?></div>
        <small style="font-size: 10px; opacity: .85;">Conserve este comprobante como constancia.</small>
    </div>
</div>

<?php if (!empty($autoprint)) { ?>
<script type="text/javascript">
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 300);
    });
</script>
<?php } ?>

</body>
</html>
