<div class="modal fade" id="basicPay" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="border-radius:12px;overflow:hidden;box-shadow:0 10px 35px rgba(0,0,0,0.25);">
			<form method="post" id="basicpay_data" class="form-horizontal">
				<!-- Modal Header -->
				<div class="modal-header bg-gradient-directional-blue white" style="padding:14px 20px;">
					<h4 class="modal-title font-weight-bold text-white mb-0" style="font-size:1.3rem;">
						<i class="fa fa-money"></i> Cobrar Pedido
					</h4>
					<button type="button" class="close text-white" data-dismiss="modal" style="opacity:0.9;">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
					</button>
				</div>

				<!-- Modal Body -->
				<div class="modal-body" style="padding:20px;">
					<p id="statusMsg"></p>

					<!-- Selector de Tipo de Comprobante -->
					<div class="form-group mb-2">
						<label class="text-bold-600 d-block mb-1" style="font-size:13px; color:#475569;">
							<i class="fa fa-file-text-o text-primary"></i> Tipo de Comprobante a Emitir:
						</label>
						<div class="row m-0" id="gp_doc_mode_group">
							<button type="button" class="btn btn-primary pay-doc-btn col mr-1 mb-1 font-weight-bold" data-mode="boleta" id="btn_mode_boleta" style="padding:8px 10px; font-size:13.5px;">
								<i class="fa fa-ticket"></i> Boleta / Sin RUC
							</button>
							<button type="button" class="btn btn-outline-primary pay-doc-btn col mb-1 font-weight-bold" data-mode="factura" id="btn_mode_factura" style="padding:8px 10px; font-size:13.5px;">
								<i class="fa fa-certificate"></i> Factura con RUC (DNIT)
							</button>
						</div>
						<input type="hidden" name="pay_doc_mode" id="pay_doc_mode" value="boleta">
					</div>

					<!-- Sección Factura con RUC (DNIT Paraguay) -->
					<div id="sec_factura_dnit" class="p-2 rounded mb-2" style="display:none; background:#f0fdf4; border:1.5px dashed #10b981;">
						<div class="form-group mb-2">
							<label for="pay_taxid" class="text-bold-600 d-block mb-1" style="color:#065f46; font-size:12.5px;">
								<i class="fa fa-id-card-o"></i> RUC o Cédula de Identidad (PY):
							</label>
							<div class="input-group">
								<input type="text" class="form-control font-weight-bold" id="pay_taxid" name="pay_taxid"
									   placeholder="Ej: 80000001 ó 4567890 (con o sin DV)" autocomplete="off" style="font-size:14.5px;">
								<div class="input-group-append">
									<button type="button" class="btn btn-success" id="btn_pay_consultar_dnit" style="background:#10b981; border-color:#10b981; font-weight:600;">
										<i class="fa fa-search"></i> Consultar DNIT
									</button>
								</div>
							</div>
							<small id="pay_dnit_status" class="text-muted" style="display:block; margin-top:3px; font-size:11px;">
								<i class="fa fa-info-circle"></i> Ingrese el RUC o Cédula y presione <strong>Consultar DNIT</strong> (o Enter) para autocompletar.
							</small>
						</div>

						<div class="form-group mb-1">
							<label for="pay_customer_name" class="text-bold-600 d-block mb-1" style="color:#065f46; font-size:12.5px;">
								Nombre / Razón Social: *
							</label>
							<input type="text" class="form-control font-weight-bold" id="pay_customer_name" name="pay_customer_name"
								   placeholder="Razón Social oficial para la factura" autocomplete="off" style="font-size:13.5px;">
						</div>
					</div>

					<!-- Nombre del pedido / llamado en caja o cocina -->
					<div class="form-group mb-2" id="sec_order_name">
						<label for="order_name" class="text-bold-600" style="font-size:13px; color:#475569;">
							<i class="fa fa-user-o text-primary"></i> A nombre de (Pedido):
						</label>
						<input type="text" class="form-control text-bold-600" name="order_name" id="order_name"
							   style="font-size:1.35rem; height:auto; padding:.4rem .6rem;"
							   placeholder="Ej: Cesar" autocomplete="off">
					</div>

					<!-- Tipo de pedido -->
					<div class="form-group mb-2">
						<label class="text-bold-600 d-block mb-1" style="font-size:13px; color:#475569;">
							<i class="fa fa-cutlery text-primary"></i> Tipo de pedido:
						</label>
						<div class="row m-0">
							<button type="button" class="btn btn-outline-primary order-type-btn col mr-1 mb-1 font-weight-bold" data-val="Pasa a buscar" style="padding:6px 4px; font-size:13px;">Pasa a buscar</button>
							<button type="button" class="btn btn-outline-primary order-type-btn col mr-1 mb-1 font-weight-bold" data-val="Delivery" style="padding:6px 4px; font-size:13px;">Delivery</button>
							<button type="button" class="btn btn-primary order-type-btn col mb-1 font-weight-bold" data-val="En local" style="padding:6px 4px; font-size:13px;">En local</button>
						</div>
						<input type="hidden" name="order_type" id="order_type" value="En local">
					</div>

					<!-- Total a cobrar -->
					<div class="text-center p-2 rounded mb-2" style="background:#f8fafc; border:1px solid #e2e8f0; margin:.5rem 0 1rem;">
						<small class="text-muted font-weight-bold" style="letter-spacing:0.5px; font-size:11.5px; text-transform:uppercase;">Total a cobrar</small>
						<h1 id="b_total" style="margin:2px 0 0 0; font-weight:900; color:#0f172a; font-size:2.2rem;"></h1>
					</div>

					<!-- Botones de pago -->
					<div class="row" id="pay_buttons">
						<div class="col-6">
							<button class="btn btn-success btn-lg btn-block" type="submit" id="pay_cash" data-type="4" style="padding:12px; font-size:1.2rem; font-weight:700; border-radius:8px;">
								<i class="fa fa-money"></i> Efectivo
							</button>
						</div>
						<div class="col-6">
							<button class="btn btn-info btn-lg btn-block" type="button" id="pay_card" style="padding:12px; font-size:1.2rem; font-weight:700; border-radius:8px;">
								<i class="fa fa-credit-card"></i> Tarjeta
							</button>
						</div>
					</div>

					<!-- Seccion tarjeta: numero de transaccion -->
					<div id="card_section" style="display:none; margin-top:1rem;">
						<div class="form-group mb-2">
							<label for="p_txn" class="text-bold-600">N&ordm; de transacci&oacute;n (ticket de la tarjeta)</label>
							<input type="text" class="form-control text-bold-600 font-weight-bold" name="p_txn" id="p_txn"
								   placeholder="Ej: 123456" autocomplete="off" style="font-size:1.2rem;">
						</div>
						<button class="btn btn-info btn-lg btn-block" type="submit" id="confirm_card" data-type="4" style="padding:12px; font-weight:700; font-size:1.15rem; border-radius:8px;">
							<i class="fa fa-print"></i> Confirmar Pago + Imprimir
						</button>
						<button type="button" class="btn btn-outline-secondary btn-block mt-1" id="cancel_card">
							<i class="fa fa-arrow-left"></i> Volver a opciones de pago
						</button>
					</div>

					<!-- Campos requeridos por el backend (ocultos) -->
					<input type="hidden" name="p_amount" id="p_amount" value="0">
					<input type="hidden" name="p_method" id="p_method" value="Cash">
					<input type="hidden" name="amount" id="balance1" value="0">
					<input type="hidden" name="b_change" id="change_p" value="0">
					<?php if (PAC) { ?>
						<select name="p_account" id="p_account" class="form-control" style="display:none">
							<?php foreach ($acc_list as $row) {
								echo '<option value="' . $row['id'] . '">' . $row['holder'] . ' / ' . $row['acn'] . '</option>';
							} ?>
						</select>
					<?php } else { ?>
						<input type="hidden" name="p_account" id="p_account" value="0">
					<?php } ?>

					<div class="row" style="display:none;">
						<div class="col-xs-12">
							<p class="payment-errors"></p>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
