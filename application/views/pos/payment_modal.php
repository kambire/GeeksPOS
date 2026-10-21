<div class="modal fade" id="basicPay" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content ">
			<form method="post" id="basicpay_data" class="form-horizontal">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Cobrar</h4>
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
					</button>
				</div>

				<!-- Modal Body -->
				<div class="modal-body">
					<p id="statusMsg"></p>

					<!-- Nombre del pedido (texto libre) -->
					<div class="form-group">
						<label for="order_name" class="text-bold-600">A nombre de</label>
						<input type="text" class="form-control text-bold-600" name="order_name" id="order_name"
							   style="font-size:1.6rem;height:auto;padding:.4rem .6rem"
							   placeholder="Ej: Cesar" autocomplete="off">
					</div>

					<!-- Tipo de pedido -->
					<div class="form-group">
						<label class="text-bold-600">Tipo de pedido</label>
						<div class="row m-0">
							<button type="button" class="btn btn-outline-primary order-type-btn col mr-1 mb-1" data-val="Pasa a buscar">Pasa a buscar</button>
							<button type="button" class="btn btn-outline-primary order-type-btn col mr-1 mb-1" data-val="Delivery">Delivery</button>
							<button type="button" class="btn btn-primary order-type-btn col mb-1" data-val="En local">En local</button>
						</div>
						<input type="hidden" name="order_type" id="order_type" value="En local">
					</div>

					<!-- Total a cobrar -->
					<div class="text-center" style="margin:.5rem 0 1rem">
						<small class="text-muted">Total a cobrar</small>
						<h1 id="b_total" style="margin:0"></h1>
					</div>

					<!-- Botones de pago -->
					<div class="row" id="pay_buttons">
						<div class="col-6">
							<button class="btn btn-success btn-lg btn-block" type="submit" id="pay_cash" data-type="4">
								<i class="icon icon-cash"></i> Efectivo
							</button>
						</div>
						<div class="col-6">
							<button class="btn btn-info btn-lg btn-block" type="button" id="pay_card">
								<i class="fa fa-credit-card"></i> Tarjeta
							</button>
						</div>
					</div>

					<!-- Seccion tarjeta: numero de transaccion -->
					<div id="card_section" style="display:none;margin-top:1rem">
						<div class="form-group">
							<label for="p_txn" class="text-bold-600">N&ordm; de transacci&oacute;n (ticket de la tarjeta)</label>
							<input type="text" class="form-control text-bold-600" name="p_txn" id="p_txn"
								   placeholder="N&ordm; de transacci&oacute;n" autocomplete="off">
						</div>
						<button class="btn btn-info btn-lg btn-block" type="submit" id="confirm_card" data-type="4">
							<i class="fa fa-print"></i> Cobrar con tarjeta + Imprimir
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
