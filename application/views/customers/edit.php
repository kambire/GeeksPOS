<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h5><?php echo $this->lang->line('Edit Customer Details') ?></h5>

            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <form method="post" id="data_form" class="form-horizontal">
                <div class="row">

                    <div class="col-md-6">
                        <h5><?php echo $this->lang->line('Billing Address') ?></h5>
                        <input type="hidden" name="id" value="<?php echo $customer['id'] ?>">


                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="product_name"><?php echo $this->lang->line('Name') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Name"
                                       class="form-control margin-bottom required" name="name"
                                       value="<?php echo $customer['name'] ?>" id="mcustomer_name">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="product_name"><?php echo $this->lang->line('Company') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Company"
                                       class="form-control margin-bottom" name="company"
                                       value="<?php echo $customer['company'] ?>">
                            </div>
                        </div>

                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="phone"><?php echo $this->lang->line('Phone') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="phone"
                                       class="form-control margin-bottom  required" name="phone"
                                       value="<?php echo $customer['phone'] ?>" id="mcustomer_phone">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label" for="email">Email</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="email"
                                       class="form-control margin-bottom required" name="email"
                                       value="<?php echo $customer['email'] ?>" id="mcustomer_email">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="product_name"><?php echo $this->lang->line('Address') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="address"
                                       class="form-control margin-bottom" name="address"
                                       value="<?php echo $customer['address'] ?>" id="mcustomer_address1">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="city"><?php echo $this->lang->line('City') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="city"
                                       class="form-control margin-bottom" name="city"
                                       value="<?php echo $customer['city'] ?>" id="mcustomer_city">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="region"><?php echo $this->lang->line('Region') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="region"
                                       class="form-control margin-bottom" name="region"
                                       value="<?php echo $customer['region'] ?>" id="region">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="country"><?php echo $this->lang->line('Country') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Country"
                                       class="form-control margin-bottom" name="country"
                                       value="<?php echo $customer['country'] ?>" id="mcustomer_country">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="postbox"><?php echo $this->lang->line('PostBox') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="region"
                                       class="form-control margin-bottom" name="postbox"
                                       value="<?php echo $customer['postbox'] ?>" id="postbox">
                            </div>
                        </div>
                        <div class="form-group row bg-light p-1 rounded" style="border: 1.5px dashed #10b981; margin-bottom: 15px;">

                            <label class="col-sm-2 col-form-label font-weight-bold"
                                   for="taxid" style="color: #065f46;"><i class="fa fa-id-card-o"></i> RUC / CI (PY)</label>

                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" placeholder="TAX ID (RUC o Cédula)"
                                           class="form-control" name="taxid" id="taxid"
                                           value="<?php echo $customer['taxid'] ?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success" id="btn_crm_dnit" style="background:#10b981; border-color:#10b981; font-weight:600;">
                                            <i class="fa fa-search"></i> Consultar DNIT
                                        </button>
                                    </div>
                                </div>
                                <small id="crm_dnit_msg" class="text-muted" style="display:block; margin-top:4px; font-size:11.5px;">
                                    <i class="fa fa-info-circle"></i> Ingrese o actualice el RUC o Cédula y presione <strong>Consultar DNIT</strong> para verificar la Razón Social.
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="postbox"><?php echo $this->lang->line('Document') ?> ID</label>

                            <div class="col-sm-6">
                                <input type="text" placeholder="Document ID"
                                       class="form-control margin-bottom b_input" name="docid"
                                       value="<?php echo $customer['docid'] ?>">
                            </div>
                        </div>
                        <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                           for="postbox"><?php echo $this->lang->line('Extra') ?> </label>
                            <div class="col-sm-6">
                                <input type="text" placeholder="Custom Field"
                                       class="form-control margin-bottom b_input" name="c_field"
                                       value="<?php echo $customer['custom1'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="customergroup"><?php echo $this->lang->line('Customer group') ?></label>

                            <div class="col-sm-6">
                                <select name="customergroup" class="form-control">
                                    <?php
                                    echo '<option value="' . $customergroup['id'] . '">' . $customergroup['title'] . ' (S)</option>';
                                    foreach ($customergrouplist as $row) {
                                        $cid = $row['id'];
                                        $title = $row['title'];
                                        echo "<option value='$cid'>$title</option>";
                                    }
                                    ?>
                                </select>


                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="customergroup">Language</label>
                            <div class="col-sm-6">
                                <select name="language" class="form-control b_input">
                                    <?php
                                    echo '<option value="' . $customer['lang'] . '">-' . ucfirst($customer['lang']) . '-</option>';
                                    echo $langs;
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                           for="Discount"><?php echo $this->lang->line('Discount') ?> </label>
                            <div class="col-sm-6">
                                <input type="text" placeholder="Custom Discount"
                                       class="form-control margin-bottom b_input" name="discount"
                                       value="<?php echo $customer['discount_c'] ?>">
                            </div>
                        </div>

                        <?php foreach ($custom_fields as $row) {
                            if ($row['f_type'] == 'text') { ?>
                                <div class="form-group row">

                                    <label class="col-sm-2 col-form-label"
                                           for="docid"><?= $row['name'] ?></label>

                                    <div class="col-sm-8">
                                        <input type="text" placeholder="<?= $row['placeholder'] ?>"
                                               class="form-control margin-bottom b_input"
                                               name="custom[<?= $row['id'] ?>]"
                                               value="<?= $row['data'] ?>">
                                    </div>
                                </div>


                            <?php }


                        }
                        ?>
                    </div>

                    <div class="col-md-6">
                        <h5><?php echo $this->lang->line('Shipping Address') ?></h5>
                        <div class="form-group row">

                            <div class="input-group mt-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="customer1"
                                           id="copy_address">
                                    <label class="custom-control-label"
                                           for="copy_address"><?php echo $this->lang->line('Same As Billing') ?></label>
                                </div>

                            </div>

                            <div class="col-sm-10">
                                <?php echo $this->lang->line("leave Shipping Address") ?>
                            </div>
                        </div>

                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="product_name"><?php echo $this->lang->line('Name') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Name"
                                       class="form-control margin-bottom" name="name_s"
                                       value="<?php echo $customer['name_s'] ?>" id="mcustomer_name_s">
                            </div>
                        </div>


                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="phone"><?php echo $this->lang->line('Phone') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="phone"
                                       class="form-control margin-bottom" name="phone_s"
                                       value="<?php echo $customer['phone_s'] ?>" id="mcustomer_phone_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label" for="email">Email</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="email"
                                       class="form-control margin-bottom" name="email_s"
                                       value="<?php echo $customer['email_s'] ?>" id="mcustomer_email_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="product_name"><?php echo $this->lang->line('Address') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="address"
                                       class="form-control margin-bottom" name="address_s"
                                       value="<?php echo $customer['address_s'] ?>" id="mcustomer_address1_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="city"><?php echo $this->lang->line('City') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="city"
                                       class="form-control margin-bottom" name="city_s"
                                       value="<?php echo $customer['city_s'] ?>" id="mcustomer_city_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="region"><?php echo $this->lang->line('Region') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="region"
                                       class="form-control margin-bottom" name="region_s"
                                       value="<?php echo $customer['region_s'] ?>" id="region_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="country"><?php echo $this->lang->line('Country') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Country"
                                       class="form-control margin-bottom" name="country_s"
                                       value="<?php echo $customer['country_s'] ?>" id="mcustomer_country_s">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="postbox"><?php echo $this->lang->line('PostBox') ?></label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="region"
                                       class="form-control margin-bottom" name="postbox_s"
                                       value="<?php echo $customer['postbox_s'] ?>" id="postbox_s">
                            </div>
                        </div>


                    </div>

                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                        <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                               value="Update customer" data-loading-text="Updating...">
                        <input type="hidden" value="customers/editcustomer" id="action-url">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $('#btn_crm_dnit').click(function (e) {
        e.preventDefault();
        var rucVal = $('#taxid').val().trim();
        if (!rucVal) {
            $('#crm_dnit_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Ingrese un RUC o Cédula para consultar.</span>');
            return;
        }

        var $btn = $(this);
        var oldHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        $('#crm_dnit_msg').html('<span class="text-info"><i class="fa fa-spinner fa-spin"></i> Consultando en DNIT / Marangatu...</span>');

        $.ajax({
            type: "POST",
            url: baseurl + 'customers/consulta_ruc',
            data: { ruc: rucVal, auto_create: 0, [crsf_token]: crsf_hash },
            dataType: 'json',
            success: function (data) {
                $btn.prop('disabled', false).html(oldHtml);
                if (data.status === 'Success') {
                    if (data.found) {
                        $('input[name="name"]').val(data.name);
                        $('input[name="company"]').val(data.company || data.name);
                        $('#taxid').val(data.taxid);
                        $('input[name="docid"]').val(data.docid);
                        if (data.phone) $('input[name="phone"]').val(data.phone);
                        if (data.email) $('input[name="email"]').val(data.email);
                        if (data.address) $('input[name="address"]').val(data.address);
                        if (data.city) $('input[name="city"]').val(data.city);

                        var estadoInfo = data.estado ? (' - Estado: <strong>' + data.estado + '</strong>') : '';
                        $('#crm_dnit_msg').html('<span class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Verificado en DNIT (DV: ' + data.dv + ')' + estadoInfo + '. Datos actualizados.</span>');
                    } else {
                        $('#taxid').val(data.taxid);
                        $('input[name="docid"]').val(data.docid);
                        $('#crm_dnit_msg').html('<span class="text-muted"><i class="fa fa-info-circle"></i> No figura como contribuyente en DNIT. Dígito sugerido: <strong>' + data.dv + '</strong>.</span>');
                    }
                } else {
                    $('#crm_dnit_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + (data.message || 'Error en la consulta') + '</span>');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(oldHtml);
                $('#crm_dnit_msg').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Error de conexión con el servicio DNIT.</span>');
            }
        });
    });

    $('#taxid').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn_crm_dnit').click();
        }
    });
});
</script>

