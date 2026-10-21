</div>
</div>
</div>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: '<?php echo $this->config->item('dformat2'); ?>'
    });
    $('[data-toggle="datepicker"]').datepicker('setDate', '<?php echo dateformat(date('Y-m-d')); ?>');

    $('#sdate').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('#sdate').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');
    $('.date30').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('.date30').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))))); ?>');

    $('.date30_plus').datepicker({autoHide: true, format: '<?php echo $this->config->item('dformat2'); ?>'});
    $('.date30_plus').datepicker('setDate', '<?php echo dateformat(date('Y-m-d', strtotime('+30 days', strtotime(date('Y-m-d'))))); ?>');



</script>
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/unslider-min.js"></script>
<script src="<?= assets_url() ?>app-assets/vendors/js/timeline/horizontal-timeline.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url() ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url() ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<script src="<?php echo assets_url(); ?>assets/myjs/jquery-ui.js"></script>
<script src="<?php echo assets_url(); ?>app-assets/vendors/js/tables/datatable/datatables.min.js"></script>

<script type="text/javascript">var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');
</script>
<script src="<?php echo assets_url('assets/myjs/custom.js') . APPVER; ?>"></script>
<script src="<?php echo assets_url('assets/myjs/basic.js') . APPVER; ?>"></script>
<script src="<?php echo assets_url('assets/myjs/control.js') . APPVER; ?>"></script>

<script type="text/javascript">
    $.ajax({

        url: baseurl + 'manager/pendingtasks',
        dataType: 'json',
        success: function (data) {
            $('#tasklist').html(data.tasks);
            $('#taskcount').html(data.tcount);

        },
        error: function (data) {
            $('#response').html('Error')
        }

    });

    // Control y cierre táctil para menús en dispositivos móviles
    $(document).ready(function () {
        // Cerrar menú lateral al tocar el fondo o fuera del menú
        $(document).on('click touchstart', function (e) {
            if ($('body').hasClass('menu-open')) {
                if (!$(e.target).closest('.main-menu, .menu-toggle').length) {
                    if (window.$.app && window.$.app.menu && typeof window.$.app.menu.hide === 'function') {
                        window.$.app.menu.hide();
                    }
                }
            }
        });

        // Al abrir la hamburguesa izquierda, colapsar el menú de 3 puntos si está abierto
        $(document).on('click', '.menu-toggle', function () {
            if ($('#navbar-mobile').hasClass('show')) {
                $('#navbar-mobile').collapse('hide');
            }
        });

        // Al abrir los 3 puntos, cerrar el menú lateral izquierdo si está abierto
        $(document).on('click', '.open-navbar-container', function () {
            if ($('body').hasClass('menu-open') && window.$.app && window.$.app.menu) {
                window.$.app.menu.hide();
            }
        });
    });
</script>


</body>
</html>
