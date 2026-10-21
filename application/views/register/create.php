<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>

        <div class="message"></div>
    </div>
    <div class=" card card-block">

        <?php
        $attributes = array('class' => 'card-body', 'id' => 'data_form');
        echo form_open('', $attributes);
        ?>


        <h3><?php echo $this->lang->line('Register') ?></h3>
        <hr>
        <p>Ingrese el monto en efectivo con que se abre la caja.</p>
        <div class="form-group row">

            <label class="col-sm-2 col-form-label text-bold-600 green"
                   for="cash"><?php echo $this->lang->line('Cash') ?></label>

            <div class="col-sm-4">
                <input type="number" step="any" placeholder="<?php echo $this->lang->line('Cash') ?>"
                       class="form-control margin-bottom round required" name="cash" value="0" autofocus>
            </div>
        </div>

        <div class="form-group row">

            <label class="col-sm-2 col-form-label"></label>

            <div class="col-sm-4">
                <input type="submit" id="submit-data" class="btn btn-blue btn-lg margin-bottom round bt"
                       value="<?php echo $this->lang->line('Add Register') ?>" data-loading-text="Adding...">
                <input type="hidden" value="register/create" id="action-url">
            </div>
        </div>

        </form>
    </div>
</div>
