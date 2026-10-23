<script>
    // En pantallas chicas (celular/tablet) usamos automáticamente el POS V2 responsive.
    // ?v2=false permite forzar el clásico aun en pantallas chicas.
    (function () {
        try {
            var sp = new URLSearchParams(window.location.search);
            if (sp.get('v2') !== 'false' && window.innerWidth < 992) {
                sp.set('v2', 'true');
                window.location.replace(window.location.pathname + '?' + sp.toString());
            }
        } catch (e) {}
    })();
</script>
<form method="post" id="data_form" class="content-body">
    <div class="sidebar-left sidebar-fixed bg-white">
        <div class="sidebar">
            <div class="sidebar-content ">
                <div class="card-body chat-fixed-search">

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="ft-search"></i></span>
                        </div>
                        <input type="text" class="form-control" id="pos-customer-box"
                               placeholder="Buscar cliente por Nombre, RUC o CI..."
                               title="Ingrese Nombre, RUC o Cédula paraguaya (DNIT)"
                               aria-describedby="button-addon2">
                        <div class="input-group-append" id="button-addon2">
                            <button class="btn btn-primary" type="button" data-toggle="modal"
                                    data-target="#Pos_addCustomer"> <?php echo $this->lang->line('Add') ?></button>
                        </div>
                    </div>
                    <div id="customer-box-result" class="col-md-12"></div>
                    <div id="customer" class="col-md-12 ml-3">
                        <div class="clientinfo">

                            <input type="hidden" name="customer_id" id="customer_id" value="1">
                            <div id="customer_name"><?php echo $this->lang->line('Default'); ?>: <strong>Walk
                                    In </strong></div>
                        </div>


                    </div>

                </div>
                <div>
                    <div class="users-list-padding media-list">

                        <br>
                        <div class="row bg-gradient-directional-purple white m-0 pt-1 pb-1">
                            <div class="col-6 ">
                                <i class="fa fa-briefcase"></i>
                                <?php echo $this->lang->line('Products') ?></th>
                            </div>
                            <div class="col-3">
                                <i class="fa fa-money"></i><?php echo $this->lang->line('Price') ?>
                            </div>
                            <div class="col-3">
                                <i
                                        class="fa fa-shopping-bag"></i> <?php echo $this->lang->line('Total') ?>
                            </div>
                        </div>
                        <div id="saman-pos2">
                            <div id="pos_items"></div>
                        </div>
                        <input type="hidden" name="total" class="form-control"
                               id="invoiceyoghtml" readonly="">
                        <hr class="mt-1">
                        <div class="row m-2" style="display:none">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Shipping') ?></strong>
                            </div>
                            <div class="col-3">
                                <input type="text" class="form-control form-control-sm shipVal"
                                       onkeypress="return isNumber(event)"
                                       placeholder="Value"
                                       name="shipping" autocomplete="off"
                                       onkeyup="billUpyog()">
                            </div>
                            <div class="col-3">
                                ( <?php echo $this->lang->line('Tax') ?> <?= $this->config->item('currency'); ?>
                                <span id="ship_final">0</span> )
                            </div>
                        </div>


                        <div class="row m-2" style="display:none">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Total Tax') ?></strong>
                            </div>
                            <div class="col-3"><?php echo currency($this->aauth->get_user()->loc);
                                ?>
                                <span id="taxr" class="mr-1">0</span>
                            </div>
                        </div>
                        <div class="row m-2" style="display:none">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Total Discount') ?></strong>
                            </div>
                            <div class="col-9"><?php echo currency($this->aauth->get_user()->loc);
                                ?>
                                <span id="discs"
                                      class="lightMode mr-1">0</span>
                                <small>(<?php echo $this->lang->line('Products') ?>)</small>
                            </div>
                        </div>
                        <div class="gp-grandtotal m-2">
                            <span class="gp-gt-label">
                                <i class="fa fa-shopping-bag"></i> <?php echo $this->lang->line('Grand Total') ?>
                            </span>
                            <span class="gp-gt-amount"><?php echo currency($this->aauth->get_user()->loc); ?>
                                <span id="bigtotal">0.00</span>
                            </span>
                        </div>
                        <div class="row m-2" style="display:none">
                            <div class="col-3">
                                <strong> <?php echo $this->lang->line('Extra') . ' ' . $this->lang->line('Discount') ?></strong>
                            </div>
                            <div class="col-3">
                                <input type="text" class="form-control form-control-sm discVal"
                                       onkeypress="return isNumber(event)"
                                       placeholder="Value" value="0"
                                       name="disc_val" autocomplete="off"
                                       onkeyup="billUpyog()">
                                <input type="hidden"
                                       name="after_disc" id="after_disc" value="0">
                            </div>
                            <div class="col-3">
                                ( <?= $this->config->item('currency'); ?>
                                <span id="disc_final">0</span> )
                            </div>
                        </div>


                        <hr>
                        <?php if ($emp['key1']) { ?>
                            <div class="col">
                                <div class="form-group form-group-sm text-g">
                                    <label for="employee"><?php echo $this->lang->line('Employee') ?></label>

                                    <select id="employee" name="employee" class="form-control form-control-sm">
                                        <?php
                                        foreach ($employee as $row) {
                                            $cid = $row['id'];
                                            $title = $row['name'];
                                            echo "<option value='$cid'>$title</option>";
                                        }
                                        ?>
                                    </select></div>
                            </div>
                        <?php } ?>
                        <div class="m-1">

                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="btn btn-outline-primary  mr-1 mb-1" id="base-tab1" data-toggle="tab"
                                       aria-controls="tab1" href="#tab1" role="tab" aria-selected="false"><i
                                                class="fa fa-trophy"></i>
                                        <?php echo $this->lang->line('Coupon') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-secondary mr-1 mb-1" id="base-tab2" data-toggle="tab"
                                       aria-controls="tab2" href="#tab2" role="tab" aria-selected="false"><i
                                                class="icon-handbag"></i>
                                        <?php echo $this->lang->line('POS') . ' ' . $this->lang->line('Settings') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-danger  mr-1 mb-1" id="base-tab3" data-toggle="tab"
                                       aria-controls="tab3" href="#tab3" role="tab" aria-selected="false"><i
                                                class="fa fa-save"></i> <?php echo $this->lang->line('Draft') ?>(s)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-outline-success mb-1" id="base-tab4" data-toggle="tab"
                                       aria-controls="tab4" href="#tab4" role="tab" aria-selected="false"><i
                                                class="fa fa-cogs"></i>
                                        <?php echo $this->lang->line('Invoice Properties') ?></a>
                                </li>
                            </ul>
                            <div class="tab-content px-1 pt-1">
                                <div class="tab-pane" id="tab1" role="tabpanel" aria-labelledby="base-tab1">
                                    <div class="input-group">

                                        <input type="text" class="form-control"
                                               id="coupon" name="coupon"><input type="hidden"
                                                                                name="coupon_amount"
                                                                                id="coupon_amount"
                                                                                value="0"><span
                                                class="input-group-addon round"> <input type="button"
                                                                                        class="apply_coupon btn btn-small btn-primary sub-btn"
                                                                                        value="<?php echo $this->lang->line('Apply') ?>"></span>


                                    </div>
                                    <input type="hidden" class="text-info" name="i_coupon" id="i_coupon"
                                           value="">
                                    <span class="text-primary text-bold-600" id="r_coupon"></span>
                                </div>
                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="base-tab2">
                                    <div class="row">
                                        <div class="col-4 blue text-xs-center"><?php echo $this->lang->line('Warehouse') ?>
                                            <select
                                                    id="v2_warehouses"
                                                    class="selectpicker form-control round teal">
                                                <?php echo $this->common->default_warehouse();
                                                echo '<option value="0">' . $this->lang->line('All') ?></option><?php foreach ($warehouse as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                                } ?>

                                            </select></div>
                                        <div class="col-4 blue text-xs-center"><?php echo $this->lang->line('Tax') ?>
                                            <select class="form-control round"
                                                    onchange="changeTaxFormat(this.value)"
                                                    id="taxformat">
                                                <?php echo $taxlist; ?>
                                            </select></div>
                                        <div class="col-4 blue text-xs-center">  <?php echo $this->lang->line('Discount') ?>
                                            <select class="form-control round teal"
                                                    onchange="changeDiscountFormat(this.value)"
                                                    id="discountFormat">

                                                <?php echo $this->common->disclist() ?>
                                            </select></div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="base-tab3">
                                    <?php foreach ($draft_list as $rowd) {
                                        echo '<li class="indigo p-1"><a href="' . base_url() . 'pos_invoices/draft?id=' . $rowd['id'] . '"> #' . $rowd['tid'] . ' (' . $rowd['invoicedate'] . ')</a></li>';
                                    } ?>
                                </div>
                                <div class="tab-pane" id="tab4" role="tabpanel" aria-labelledby="base-tab4">
                                    <div class="form-group row">
                                        <div class="col-sm-3"><label for="invocieno"
                                                                     class="caption"><?php echo $this->lang->line('Invoice Number') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-file-text-o"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Invoice #"
                                                       name="invocieno" id="invocieno"
                                                       value="<?php echo $lastinvoice + 1 ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3"><label for="invocieno"
                                                                     class="caption"><?php echo $this->lang->line('Reference') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-bookmark-o"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="Reference #"
                                                       name="refer">
                                            </div>
                                        </div>


                                        <div class="col-sm-3"><label for="invociedate"
                                                                     class="caption"><?php echo $this->lang->line('Invoice Date'); ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar4"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control required"
                                                       placeholder="Billing Date" name="invoicedate"
                                                       data-toggle="datepicker"
                                                       autocomplete="false">
                                            </div>
                                        </div>
                                        <div class="col-sm-3"><label for="invocieduedate"
                                                                     class="caption"><?php echo $this->lang->line('Invoice Due Date') ?></label>

                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar-o"
                                                                                     aria-hidden="true"></span>
                                                </div>
                                                <input type="text" class="form-control required" id="tsn_due"
                                                       name="invocieduedate"
                                                       placeholder="Due Date" data-toggle="datepicker"
                                                       autocomplete="false">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <?php echo $this->lang->line('Payment Terms') ?> <select
                                                    name="pterms"
                                                    class="selectpicker form-control"><?php foreach ($terms as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
                                                } ?>

                                            </select>
                                            <?php if ($exchange['active'] == 1) {
                                                echo $this->lang->line('Payment Currency client') ?>
                                            <?php } ?>
                                            <?php if ($exchange['active'] == 1) {
                                                ?>
                                                <select name="mcurrency"
                                                        class="selectpicker form-control">
                                                <option value="0">Default</option>
                                                <?php foreach ($currency as $row) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['symbol'] . ' (' . $row['code'] . ')</option>';
                                                } ?>

                                                </select><?php } ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="toAddInfo"
                                                   class="caption"><?php echo $this->lang->line('Invoice Note') ?></label>
                                            <textarea class="form-control" name="notes" rows="2"></textarea>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="content-right">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="chat-app-window">
                    <div class="row ">


                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa fa-barcode p-0"></i>&nbsp;<input type="checkbox"
                                                                                      aria-label="Enable BarCode"
                                                                                      value="1" id="bar_only">
                                    </div>
                                </div>
                                <input type="text" class="form-control text-center round mousetrap"
                                       name="product_barcode"
                                       placeholder="Enter Product name, code or scan barcode" id="v2_search_bar"
                                       autocomplete="off" autofocus="autofocus">
                            </div>


                        </div>
                        <div class="col-md-3  grey text-xs-center"><select
                                    id="v2_categories"
                                    class="selectpicker form-control round teal">
                                <option value="0"><?php echo $this->lang->line('All') ?></option><?php
                                foreach ($cat as $row) {
                                    $cid = $row['id'];
                                    $title = $row['title'];
                                    echo "<option value='$cid'>$title</option>";
                                }
                                ?>
                            </select></div>


                    </div>
                    <hr class="white">


                    <div class="row m-0">
                        <div class="col-md-12 pt-0 " id="pos_item">
                            <!-- pos items -->
                        </div>
                    </div>
                </section>
                <section class="chat-app-form">
                    <div class="form-group text-center">
                        <!-- Button Group with Icons in different sizes -->
                        <div class="btn-group btn-group-lg" role="group">
                            <button type="button" class="possubmit btn btn-warning"><i
                                        class="fa fa-save"></i> <?php echo $this->lang->line('Draft') ?> </button>
                            <button type="button" class="btn btn-success possubmit3" data-type="6" data-toggle="modal"
                                    data-target="#basicPay"><i
                                        class="fa fa-money"></i> <?php echo $this->lang->line('Payment') ?>
                            </button> <?php

                            if ($enable_card['url']) { ?>
                                <button type="button" class="btn btn-primary possubmit2" data-type="4"
                                        data-toggle="modal" data-target="#basicPay"><i
                                            class="fa fa-credit-card"></i> <?php echo $this->lang->line('Card') ?>
                                </button>     <?php } ?>


                        </div>
                        <a href="<?= base_url('stockreturn/create_client') ?>" class="red float-right"><i
                                    class="fa fa-reply-all"></i></a>
                    </div>


                </section>
            </div>
        </div>
    </div>
    <input type="hidden" value="pos_invoices/action" id="action-url">
    <input type="hidden" value="0" id="subttlform"
           name="subtotal">
    <input type="hidden" value="search" id="billtype">
    <input type="hidden" value="0" name="counter" id="ganak">
    <input type="hidden" value="0" id="custom_discount">
    <input type="hidden" value="<?php echo currency($this->aauth->get_user()->loc); ?>" name="currency">
    <input type="hidden" value="<?= $taxdetails['handle']; ?>" name="taxformat" id="tax_format">
    <input type="hidden" value="<?= $taxdetails['format']; ?>" name="tax_handle" id="tax_status">
    <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">
    <input type="hidden" value="<?= $this->common->disc_status()['disc_format']; ?>" name="discountFormat"
           id="discount_format">
    <input type="hidden" value="<?= amountFormat_general($this->common->disc_status()['ship_rate']); ?>" name="shipRate"
           id="ship_rate">
    <input type="hidden" value="<?= $this->common->disc_status()['ship_tax']; ?>" name="ship_taxtype"
           id="ship_taxtype">
    <input type="hidden" value="0" name="ship_tax" id="ship_tax">
</form>
<audio id="beep" src="<?= assets_url() ?>assets/js/beep.wav" autoplay="false"></audio>

<div class="modal fade" id="Pos_addCustomer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">
            <form method="post" id="product_action" class="form-horizontal">
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-directional-blue white">
                    <i class="icon-user-plus"></i> <?php echo $this->lang->line('Add Customer') ?></h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <p id="statusMsg"></p><input type="hidden" name="mcustomer_id" id="mcustomer_id" value="0">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5><?php echo $this->lang->line('Billing Address') ?></h5>

                            <!-- Consulta RUC / Cédula DNIT Paraguay -->
                            <div class="form-group row p-1 rounded" style="background: #f0fdf4; border: 1.5px dashed #10b981; margin: 0 0 14px 0;">
                                <label class="col-sm-3 col-form-label font-weight-bold" for="mcustomer_taxid" style="color: #065f46;">
                                    <i class="fa fa-id-card-o"></i> RUC / CI (PY):
                                </label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" placeholder="Ej: 80000001 ó 4567890 (con o sin DV)"
                                               class="form-control" name="taxid" id="mcustomer_taxid" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success" id="btn_consultar_dnit" style="background:#10b981; border-color:#10b981; font-weight:600;">
                                                <i class="fa fa-search"></i> Consultar DNIT
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="docid" id="mcustomer_docid">
                                    <small id="dnit_status_msg" class="text-muted" style="display:block; margin-top: 4px; font-size: 11.5px;">
                                        <i class="fa fa-info-circle"></i> Ingrese Cédula o RUC y presione <strong>Consultar DNIT</strong> para autocompletar la Razón Social.
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-bold"
                                       for="name"><?php echo $this->lang->line('Name') ?> / Razón Social *</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="Nombre completo o Razón Social"
                                           class="form-control margin-bottom" id="mcustomer_name" name="name"
                                           required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="phone"><?php echo $this->lang->line('Phone') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Phone"
                                           class="form-control margin-bottom" name="phone" id="mcustomer_phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="email"><?php echo $this->lang->line('Email') ?></label>
                                <div class="col-sm-10">
                                    <input type="email" placeholder="Email"
                                           class="form-control margin-bottom" name="email"
                                           id="mcustomer_email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="address"><?php echo $this->lang->line('Address') ?></label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="Address"
                                           class="form-control margin-bottom " name="address"
                                           id="mcustomer_address1">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                       for="customergroup"><?php echo $this->lang->line('Group') ?></label>
                                <div class="col-sm-10">
                                    <select name="customergroup" class="form-control">
                                        <?php
                                        foreach ($customergrouplist as $row) {
                                            $cid = $row['id'];
                                            $title = $row['title'];
                                            echo "<option value='$cid'>$title</option>";
                                        }
                                        ?>
                                    </select>


                                </div>
                            </div>

                            <?php
                            if (is_array($custom_fields_c)) {
                                foreach ($custom_fields_c as $row) {
                                    if ($row['f_type'] == 'text') { ?>
                                        <div class="form-group row">

                                            <label class="col-sm-2 col-form-label"
                                                   for="docid"><?= $row['name'] ?></label>

                                            <div class="col-sm-8">
                                                <input type="text" placeholder="<?= $row['placeholder'] ?>"
                                                       class="form-control margin-bottom b_input"
                                                       name="custom[<?= $row['id'] ?>]">
                                            </div>
                                        </div>


                                    <?php }
                                }
                            }
                            ?>
                        </div>


                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('Close') ?></button>
                    <input type="submit" id="mclient_add" class="btn btn-primary submitBtn" value="ADD"/ >
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('pos/payment_modal',['acc_list'=>$acc_list]); ?>
<?php $this->load->view('pos/whatsapp_drawer'); ?>
<div class="modal fade" id="register" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Your Register') ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>

            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center m-1"><?php echo $this->lang->line('Active') ?> - <span id="r_date"></span></div>


                <div class="row">
                    <div class="col-6">
                        <div class="form-group  text-bold-600 green">
                            <label for="amount"><?php echo $this->lang->line('Cash') ?>
                                (<?= $this->config->item('currency'); ?>)
                            </label>
                            <input type="number" class="form-control green" id="r_cash"
                                   value="0.00"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-5 col-md-5 pull-right">
                        <div class="form-group text-bold-600 blue">
                            <label for="b_change blue"><?php echo $this->lang->line('Card') ?></label>
                            <input
                                    type="number"
                                    class="form-control blue"
                                    id="r_card" value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group  text-bold-600 indigo">
                            <label for="amount"><?php echo $this->lang->line('Bank') ?>
                            </label>
                            <input type="number" class="form-control indigo" id="r_bank"
                                   value="0.00"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-5 col-md-5 pull-right">
                        <div class="form-group text-bold-600 red">
                            <label for="b_change"><?php echo $this->lang->line('Change') ?>(-)</label>
                            <input
                                    type="number"
                                    class="form-control red"
                                    id="r_change" value="0" readonly>
                        </div>
                    </div>
                </div>


                <div class="row" style="display:none;">
                    <div class="col-xs-12">
                        <p class="payment-errors"></p>
                    </div>
                </div>


                <!-- shipping -->


            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div class="modal fade" id="close_register" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Close') ?><?php echo $this->lang->line('Your Register') ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">
                        <a href="<?= base_url() ?>/register/close" class="btn btn-danger btn-lg btn-block"
                           type="submit"
                        ><i class="icon icon-arrow-circle-o-right"></i> <?php echo $this->lang->line('Yes') ?></a>
                    </div>
                    <div class="col-4"></div>
                </div>

            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div class="modal fade" id="stock_alert" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content ">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title"><?php echo $this->lang->line('Stock Alert') ?> !</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo $this->lang->line('Close') ?></span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <div class="row p-1">
                    <div class="alert alert-danger mb-2" role="alert">
                        <strong>Oh snap!</strong> <?php echo $this->lang->line('order or edit the stock') ?>
                    </div>
                </div>

            </div>
            <!-- Modal Footer -->


        </div>
    </div>
</div>
<div id="shortkeyboard" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">ShortCuts</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>Alt+X</td>
                        <td>Focus to products search</td>
                    </tr>
                    <tr>
                        <td>Alt+C</td>
                        <td>Focus to customer search</td>
                    </tr>

                    <tr>
                        <td>Alt+S (twice)</td>
                        <td>PayNow + Thermal Print</td>
                    </tr>
                    <tr>
                        <td>Alt+Z</td>
                        <td>Make Card Payment</td>
                    </tr>
                    <tr>
                        <td>Alt+Q</td>
                        <td>Select First product</td>
                    </tr>
                    <tr>
                        <td>Alt+N</td>
                        <td>Create New Invoice</td>
                    </tr>


                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

</div>
<script type="text/javascript">
    var wait = true;
    $.ajax({
        url: baseurl + 'search_products/v2_pos_search',
        dataType: 'html',
        method: 'POST',
        data: 'cid=' + $('#v2_categories').val() + '&wid=' + $('#v2_warehouses option:selected').val() + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
        success: function (data) {
            $('#pos_item').html(data);

        }
    });

    function update_register() {
        $.ajax({
            url: baseurl + 'register/status',
            dataType: 'json',
            data: crsf_token + '=' + crsf_hash,
            success: function (data) {
                $('#r_cash').val(data.cash);
                $('#r_card').val(data.card);
                $('#r_bank').val(data.bank);
                $('#r_change').val(data.change);
                $('#r_date').text(data.date);
            }
        });
    }

    update_register();
    $(".possubmit").on("click", function (e) {
        e.preventDefault();
        var o_data = $("#data_form").serialize() + '&type=' + $(this).attr('data-type');
        var action_url = $('#action-url').val();
        addObject(o_data, action_url);
    });

    $(".possubmit2").on("click", function (e) {
        e.preventDefault();
        var roundoff = parseFloat(accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal)).toFixed(two_fixed);

        <?php
        $round_off = $this->custom->api_config(4);
        if ($round_off['other'] == 'PHP_ROUND_HALF_UP') {
            echo ' roundoff=Math.ceil(roundoff);';
        } elseif ($round_off['other'] == 'PHP_ROUND_HALF_DOWN') {
            echo ' roundoff=Math.floor(roundoff);';
        }
        ?>
        $('#b_total').html(' <?= $this->config->item('currency'); ?> ' + accounting.formatNumber(roundoff));
        $('#p_amount').val(accounting.formatNumber(roundoff));
        $('#pay_buttons').hide();
        $('#card_section').show();
        setTimeout(function() { $('#p_txn').focus(); }, 350);
    });

    $(".possubmit3").on("click", function (e) {
        e.preventDefault();
        var roundoff = parseFloat(accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal)).toFixed(two_fixed);

        <?php
        $round_off = $this->custom->api_config(4);
        if ($round_off['other'] == 'PHP_ROUND_HALF_UP') {
            echo ' roundoff=Math.ceil(roundoff);';
        } elseif ($round_off['other'] == 'PHP_ROUND_HALF_DOWN') {
            echo ' roundoff=Math.floor(roundoff);';
        }
        ?>
        $('#b_total').html(' <?= $this->config->item('currency'); ?> ' + accounting.formatNumber(roundoff));
        $('#p_amount').val(accounting.formatNumber(roundoff));
        $('#card_section').hide();
        $('#pay_buttons').show();
    });

    function update_pay_pos() {
        var am_pos = accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal);
        var ttl_pos = accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal);
        <?php
        $round_off = $this->custom->api_config(4);
        if ($round_off['other'] == 'PHP_ROUND_HALF_UP') {
            echo ' ttl_pos=Math.ceil(ttl_pos);';
        } elseif ($round_off['other'] == 'PHP_ROUND_HALF_DOWN') {
            echo ' ttl_pos=Math.floor(ttl_pos);';
        }
        ?>

        var due = parseFloat(ttl_pos - am_pos).toFixed(two_fixed);

        if (due >= 0) {
            $('#balance1').val(accounting.formatNumber(due));
            $('#change_p').val(0);
        } else {
            due = due * (-1)
            $('#balance1').val(0);
            $('#change_p').val(accounting.formatNumber(due));
        }
    }

    // Volver de tarjeta a las opciones de pago
    $('#cancel_card').on('click', function (e) {
        e.preventDefault();
        $('#card_section').hide();
        $('#pay_buttons').slideDown();
        $('#p_txn').val('');
    });
    // === Sistema de Impresión Térmica Directa (Sin Ventanas Emergentes) & Cobro Rápido ===
    var pos_last_ticket_url = '';
    var pos_last_create_url = '<?= base_url("pos_invoices/create") ?>';

    // Impresión silenciosa en iframe de fondo (NO abre pestañas ni ventanas popup)
    function imprimirTicketIframe(url) {
        if (!url) return;
        var printUrl = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'print=1';
        var iframe = document.getElementById('gp_auto_print_frame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'gp_auto_print_frame';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '1px';
            iframe.style.height = '1px';
            iframe.style.opacity = '0.01';
            iframe.style.border = '0';
            iframe.style.zIndex = '-9999';
            document.body.appendChild(iframe);
        }
        iframe.src = printUrl;
    }

    // Mostrar notificación toast estilizada y no invasiva (en la esquina superior)
    function mostrarToastVenta(data) {
        $('#pos_sale_toast').remove();
        try {
            window.dispatchEvent(new CustomEvent('pos_sale_completed', { detail: data }));
        } catch(e) {}
        var sifenInfo = data.sifen_numero ? (' • <span class="badge badge-success" style="font-size:11px;">Factura SIFEN ' + data.sifen_numero + '</span>') : '';
        var toastHtml = '<div id="pos_sale_toast" style="position:fixed;top:20px;right:20px;z-index:99999;background:#065f46;color:#fff;padding:12px 18px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.3);font-size:14px;display:flex;align-items:center;gap:12px;border-left:5px solid #10b981;">' +
            '<i class="fa fa-check-circle" style="font-size:22px;color:#34d399;"></i>' +
            '<div>' +
                '<div style="font-weight:700;">¡Venta Cobrada con Éxito! Ticket #' + (data.tid || data.invoice_id) + sifenInfo + '</div>' +
                '<div style="font-size:12px;opacity:0.9;"><i class="fa fa-print"></i> Ticket impreso... ' +
                    '<a href="javascript:imprimirTicketIframe(\'' + data.ticket_url + '\')" style="color:#6ee7b7;text-decoration:underline;margin-left:6px;font-weight:bold;">Reimprimir</a>' +
                    '<a href="javascript:toggleWhatsAppDrawer(true)" style="background:#25d366;color:#fff;padding:2px 8px;border-radius:4px;margin-left:8px;font-weight:bold;text-decoration:none;"><i class="fa fa-whatsapp"></i> WhatsApp</a>' +
                '</div>' +
            '</div>' +
            '<button type="button" onclick="$(\'#pos_sale_toast\').fadeOut()" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;margin-left:8px;">&times;</button>' +
        '</div>';
        $('body').append(toastHtml);
        setTimeout(function() {
            $('#pos_sale_toast').fadeOut(function() { $(this).remove(); });
        }, 6000);
    }

    // Limpiar el carrito del POS inmediatamente tras el cobro exitoso
    function limpiarCarritoPOS() {
        $('#pos_items').empty();
        $('#ganak').val(0);
        $('#subttlform').val(0);
        $('#invoiceyoghtml').val(0);
        $('#bigtotal').html('<?= $this->config->item("currency"); ?> 0');
        $('#total_items_count').text('0');
        $('#total_items_count_val').val(0);
        if (typeof resetDefaultCustomer === 'function') resetDefaultCustomer();
        $('#order_name').val('');
        $('#pay_taxid').val('');
        $('#pay_customer_name').val('');
        $('#pay_dnit_status').html('<i class="fa fa-info-circle"></i> Ingrese el RUC o Cédula y presione <strong>Consultar DNIT</strong> (o Enter) para autocompletar.');
        $('#btn_mode_boleta').click();
        $('#p_txn').val('');
        $('#card_section').hide();
        $('#pay_buttons').show();
        $('#statusMsg').html('');
        setTimeout(function () {
            if (typeof update_register === 'function') update_register();
        }, 1500);
        $('#v2_search_bar').val('').focus();
    }

    // Cobrar: efectivo (1 clic) o tarjeta (con N° de transacción). 100% automático.
    function cobrarPOS(method) {
        var errorNum = farmCheck();
        if (errorNum > 0) {
            $('#statusMsg').html('<div class="alert alert-danger py-1 mb-1"><strong>Error:</strong> Complete los campos requeridos antes de cobrar.</div>');
            return;
        }

        var total_val = parseFloat(accounting.unformat($('#invoiceyoghtml').val(), accounting.settings.number.decimal));
        if (isNaN(total_val) || total_val <= 0) {
            $('#statusMsg').html('<div class="alert alert-warning py-1 mb-1"><strong>Atención:</strong> Debe agregar productos al carrito antes de cobrar.</div>');
            return;
        }

        var docMode = $('#pay_doc_mode').val() || 'boleta';
        var payTaxid = $('#pay_taxid').val().trim();
        var payCustName = $('#pay_customer_name').val().trim();

        if (docMode === 'factura' && !payTaxid) {
            $('#statusMsg').html('<div class="alert alert-warning py-1 mb-1"><strong>Atención:</strong> Ingrese el número de RUC o Cédula para emitir la Factura.</div>');
            $('#pay_taxid').focus();
            return;
        }

        // Deshabilitar botones durante el proceso para evitar duplicados
        $('#pay_cash, #confirm_card, #pay_card').prop('disabled', true);
        var origCash = $('#pay_cash').html();
        var origCard = $('#confirm_card').html();
        $('#pay_cash').html('<i class="fa fa-spinner fa-spin"></i> Cobrando e imprimiendo...');
        $('#confirm_card').html('<i class="fa fa-spinner fa-spin"></i> Cobrando e imprimiendo...');
        $('#statusMsg').html('');

        var o_data = $("#data_form").serialize()
            + '&p_amount=' + accounting.unformat($('#p_amount').val(), accounting.settings.number.decimal)
            + '&p_method=' + method
            + '&type=4&printnow=1'
            + '&account=' + $('#p_account').val()
            + '&employee=' + $("#employee option:selected").val()
            + '&pay_doc_mode=' + encodeURIComponent(docMode)
            + '&pay_taxid=' + encodeURIComponent(payTaxid)
            + '&pay_customer_name=' + encodeURIComponent(payCustName)
            + '&order_name=' + encodeURIComponent($('#order_name').val() || payCustName)
            + '&order_type=' + encodeURIComponent($('#order_type').val())
            + '&p_txn=' + encodeURIComponent($('#p_txn').val());

        var action_url = $('#action-url').val();

        $.ajax({
            url: baseurl + action_url,
            type: 'POST',
            data: o_data + '&' + crsf_token + '=' + crsf_hash,
            dataType: 'json',
            success: function (data) {
                $('#pay_cash, #confirm_card, #pay_card').prop('disabled', false);
                $('#pay_cash').html(origCash);
                $('#confirm_card').html(origCard);

                if (data.status === 'Success') {
                    // 1. Cerrar el modal de cobro inmediatamente
                    $('#basicPay').modal('hide');

                    pos_last_ticket_url = data.ticket_url;
                    pos_last_create_url = data.create_url || ('<?= base_url("pos_invoices/create") ?>');

                    // 2. Disparar impresión térmica silenciosa en iframe (SIN ventanas emergentes)
                    imprimirTicketIframe(data.ticket_url);

                    // 3. Mostrar confirmación en toast sutil en la esquina superior
                    mostrarToastVenta(data);

                    // 4. Limpiar el carrito del POS inmediatamente para la siguiente venta
                    limpiarCarritoPOS();

                } else {
                    $('#statusMsg').html('<div class="alert alert-danger py-1 mb-1"><strong>Error al cobrar:</strong> ' + (data.message || 'No se pudo procesar la venta.') + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#pay_cash, #confirm_card, #pay_card').prop('disabled', false);
                $('#pay_cash').html(origCash);
                $('#confirm_card').html(origCard);
                $('#statusMsg').html('<div class="alert alert-danger py-1 mb-1"><strong>Error de comunicación:</strong> ' + error + '</div>');
            }
        });
    }

    // Selector de Modo de Comprobante: Boleta vs Factura con RUC
    $(document).on('click', '.pay-doc-btn', function (e) {
        e.preventDefault();
        $('.pay-doc-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        var mode = $(this).data('mode');
        $('#pay_doc_mode').val(mode);

        if (mode === 'factura') {
            $('#sec_factura_dnit').slideDown();
            setTimeout(function() { $('#pay_taxid').focus(); }, 200);
        } else {
            $('#sec_factura_dnit').slideUp();
            $('#order_name').focus();
        }
    });

    // Consulta DNIT dentro del modal de cobro
    $(document).on('click', '#btn_pay_consultar_dnit', function (e) {
        e.preventDefault();
        var rucVal = $('#pay_taxid').val().trim();
        if (!rucVal) {
            $('#pay_dnit_status').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Ingrese un RUC o Cédula para consultar.</span>');
            return;
        }

        var $btn = $(this);
        var oldHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');
        $('#pay_dnit_status').html('<span class="text-info"><i class="fa fa-spinner fa-spin"></i> Consultando en el padrón DNIT / Marangatu...</span>');

        $.ajax({
            type: "POST",
            url: baseurl + 'pos_invoices/consulta_ruc',
            data: { ruc: rucVal, auto_create: 0, [crsf_token]: crsf_hash },
            dataType: 'json',
            success: function (data) {
                $btn.prop('disabled', false).html(oldHtml);
                if (data.status === 'Success') {
                    if (data.found) {
                        $('#pay_taxid').val(data.taxid);
                        $('#pay_customer_name').val(data.name);
                        if (!$('#order_name').val()) {
                            $('#order_name').val(data.name);
                        }
                        var estadoBadge = data.estado ? (' <span class="badge badge-success" style="font-size:10px;">' + data.estado + '</span>') : '';
                        $('#pay_dnit_status').html('<span class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Verificado en DNIT (DV: ' + data.dv + ')' + estadoBadge + '. Razón social autocompletada.</span>');
                    } else {
                        $('#pay_taxid').val(data.taxid);
                        $('#pay_dnit_status').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Cédula no figura en el padrón DNIT. DV sugerido: <strong>' + data.dv + '</strong>. Ingrese el nombre manualmente.</span>');
                        $('#pay_customer_name').focus();
                    }
                } else {
                    $('#pay_dnit_status').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + (data.message || 'Error en la consulta') + '</span>');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(oldHtml);
                $('#pay_dnit_status').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> No se pudo conectar con DNIT. Complete los datos manualmente.</span>');
            }
        });
    });

    $(document).on('keypress', '#pay_taxid', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn_pay_consultar_dnit').click();
        }
    });

    // Al abrir el modal de cobro, sincronizar con el cliente si ya había uno seleccionado
    $('#basicPay').on('show.bs.modal', function () {
        var curCid = $('#customer_id').val();
        if (curCid && curCid != '1') {
            $('#btn_mode_factura').click();
            var curText = $('#customer_name').text().replace('Quitar cliente / Consumidor Final', '').trim();
            if (!$('#pay_customer_name').val()) {
                $('#pay_customer_name').val(curText);
            }
            if (!$('#order_name').val()) {
                $('#order_name').val(curText);
            }
        }
    });

    // Selector de tipo de pedido
    $(document).on('click', '.order-type-btn', function (e) {
        e.preventDefault();
        $('.order-type-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('#order_type').val($(this).data('val'));
    });

    // Efectivo: cobra el total e imprime automáticamente
    $('#pay_cash').on('click', function (e) { e.preventDefault(); cobrarPOS('Cash'); });

    // Tarjeta: muestra el campo para el N de transacción
    $('#pay_card').on('click', function (e) {
        e.preventDefault();
        $('#pay_buttons').hide();
        $('#card_section').slideDown();
        $('#p_txn').focus();
    });

    // Confirmar cobro con tarjeta
    $('#confirm_card').on('click', function (e) { e.preventDefault(); cobrarPOS('Card Swipe'); });

    // Prevenir submit nativo del formulario modal
    $('#basicpay_data').on('submit', function (e) { e.preventDefault(); });

    // Enter en campos del modal para máxima agilidad
    $('#order_name').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#pay_cash').click();
        }
    });
    $('#p_txn').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#confirm_card').click();
        }
    });

    // Al cerrar el modal de cobro, restablecer estado visual
    $('#basicPay').on('hidden.bs.modal', function () {
        $('#card_section').hide(); $('#pay_buttons').show(); $('#p_txn').val(''); $('#statusMsg').html('');
    });

    // Manejador genérico de botones de reimpresión de ticket
    $(document).on('click', '.gp-btn-print-ticket', function (e) {
        var href = $(this).attr('href');
        if (href) {
            e.preventDefault();
            imprimirTicketIframe(href);
        }
    });

    $('#v2_categories').change(function () {
        var whr = $('#v2_warehouses option:selected').val();
        var cat = $('#v2_categories option:selected').val();
        $.ajax({
            type: "POST",
            url: baseurl + 'search_products/v2_pos_search',
            data: 'wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash,
            beforeSend: function () {
                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
            },
            success: function (data) {

                $("#pos_item").html(data);

            }
        });
    });
    $('#v2_warehouses').change(function () {
        var whr = $('#v2_warehouses option:selected').val();
        var cat = $('#v2_categories option:selected').val();
        $.ajax({
            type: "POST",
            url: baseurl + 'search_products/v2_pos_search',
            data: 'wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash,
            beforeSend: function () {
                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
            },
            success: function (data) {

                $("#pos_item").html(data);

            }
        });
    })
    $(document).ready(function () {

        if (localStorage.bar_only && localStorage.bar_only != '') {
            $('#bar_only').attr('checked', 'checked');

        } else {
            $('#bar_only').removeAttr('checked');
        }

        $('#bar_only').click(function () {

            if ($('#bar_only').is(':checked')) {

                localStorage.bar_only = $('#bar_only').val();
            } else {
                localStorage.bar_only = '';
            }
            $('#v2_search_bar').attr('readonly', false);
        });

        Mousetrap.bind('alt+x', function () {
            $('#v2_search_bar').focus();
        });
        Mousetrap.bind('alt+c', function () {
            $('#pos-customer-box').focus();
        });

        Mousetrap.bind('alt+z', function () {
            $('.possubmit2').click();
        });
        Mousetrap.bind('alt+n', function () {
            window.location.href = "<?=base_url('pos_invoices/create') ?>";
        });
        Mousetrap.bind('alt+q', function () {
            $('#posp0').click();
            $('#v2_search_bar').val('');
        });
        Mousetrap.bind('alt+s', function () {
            if ($('#basicPay').hasClass('show')) {
                $('#pay_cash').click();
            } else {
                $('.possubmit3').click();
            }

        });


        $('#v2_search_bar').keyup(function (event) {

            if (!$(this).attr('readonly')) {
                //$('#v2_search_bar').keyup(function () {
                var whr = $('#v2_warehouses option:selected').val();
                var cat = $('#v2_categories option:selected').val();
                if (this.value.length > 2) {
                    $.ajax({
                        type: "POST",
                        url: baseurl + 'search_products/v2_pos_search',
                        data: 'name=' + $(this).val() + '&wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
                        beforeSend: function () {
                            $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
                        },
                        success: function (data) {
                            $("#pos_item").html(data);

                        }
                    });

                }
            }

            if (event.keyCode == 13 && !$('#v2_search_bar').attr('readonly')) {
                $('#v2_search_bar').attr('readonly', true);
                wait = false;
                def_timeout(1000).then(function () {
                    $('#posp0').click();

                    def_timeout(1800).then(function () {


                        var whr = $('#v2_warehouses option:selected').val();
                        var cat = $('#v2_categories option:selected').val();
                        $.ajax({
                            type: "POST",
                            url: baseurl + 'search_products/v2_pos_search',
                            data: 'name=' + $(this).val() + '&wid=' + whr + '&cid=' + cat + '&' + crsf_token + '=' + crsf_hash + '&bar=' + $('#bar_only').prop('checked'),
                            beforeSend: function () {
                                $("#customer-box").css("background", "#FFF url(" + baseurl + "assets/custom/load-ring.gif) no-repeat 165px");
                            },
                            success: function (data) {
                                $("#pos_item").html(data);
                                $('#v2_search_bar').attr('readonly', false);
                                $('#v2_search_bar').val('');
                            }

                        });
                    });


                });


            }
        });

        // Consulta y Autocompletado DNIT Paraguay en modal de agregar cliente
        $(document).on('click', '#btn_consultar_dnit', function (e) {
            e.preventDefault();
            var rucVal = $('#mcustomer_taxid').val().trim();
            if (!rucVal) {
                $('#dnit_status_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Ingrese un RUC o Cédula para consultar.</span>');
                return;
            }

            var $btn = $(this);
            var oldHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');
            $('#dnit_status_msg').html('<span class="text-info"><i class="fa fa-spinner fa-spin"></i> Consultando en el padrón DNIT / Marangatu...</span>');

            $.ajax({
                type: "POST",
                url: baseurl + 'pos_invoices/consulta_ruc',
                data: { ruc: rucVal, auto_create: 0, [crsf_token]: crsf_hash },
                dataType: 'json',
                success: function (data) {
                    $btn.prop('disabled', false).html(oldHtml);
                    if (data.status === 'Success') {
                        if (data.found) {
                            if (data.source === 'local') {
                                var safeName = data.name.replace(/'/g, "\\'");
                                var safeTax = (data.taxid || '').replace(/'/g, "\\'");
                                $('#dnit_status_msg').html('<span class="text-warning font-weight-bold"><i class="fa fa-info-circle"></i> Cliente ya registrado en el sistema (ID #' + data.customer_id + '). ' +
                                    '<button type="button" class="btn btn-sm btn-success ml-1" onclick="PselectCustomerFull(' + data.customer_id + ', \'' + safeName + '\', \'' + safeTax + '\', ' + data.discount + '); $(\'#Pos_addCustomer\').modal(\'hide\');">' +
                                    '<i class="fa fa-check"></i> Seleccionar en Venta</button></span>');
                                $('#mcustomer_name').val(data.name);
                                $('#mcustomer_taxid').val(data.taxid);
                                $('#mcustomer_docid').val(data.docid);
                                if (data.phone) $('#mcustomer_phone').val(data.phone);
                                if (data.email) $('#mcustomer_email').val(data.email);
                                if (data.address) $('#mcustomer_address1').val(data.address);
                            } else {
                                $('#mcustomer_name').val(data.name);
                                $('#mcustomer_taxid').val(data.taxid);
                                $('#mcustomer_docid').val(data.docid);
                                $('#dnit_status_msg').html('<span class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Verificado en DNIT: ' + data.estado + ' (DV: ' + data.dv + '). Razón Social autocompletada.</span>');
                            }
                        } else {
                            $('#mcustomer_taxid').val(data.taxid);
                            $('#mcustomer_docid').val(data.docid);
                            $('#dnit_status_msg').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Cédula no registrada como contribuyente en DNIT. Dígito sugerido: <strong>' + data.dv + '</strong>. Ingrese el nombre manualmente.</span>');
                            $('#mcustomer_name').focus();
                        }
                    } else {
                        $('#dnit_status_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + (data.message || 'Error en la consulta') + '</span>');
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html(oldHtml);
                    $('#dnit_status_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> No se pudo conectar con el servicio DNIT. Complete los datos manualmente.</span>');
                }
            });
        });

        $(document).on('keypress', '#mcustomer_taxid', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btn_consultar_dnit').click();
            }
        });
    });
</script>
