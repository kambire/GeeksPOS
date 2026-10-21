<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>
    <div class="card-body">
        <h5>Software Update</h5>
        <hr>
        <p>Current version: <strong>V <?= htmlspecialchars($version) ?> (build <?= htmlspecialchars($build) ?>)</strong></p>
        <p>Update source: GitHub repository <code><?= htmlspecialchars(UPDATE_REPO) ?></code></p>
        <p class="text-muted">To publish an update, create a GitHub release with an asset named
            <code>update_&lt;build&gt;.zip</code> containing <code>files.json</code> and, if needed,
            <code>update_build_&lt;build&gt;.sql</code>.</p>
        <button id="btn_check" class="btn btn-primary">1. Check &amp; Download Update</button>
        <button id="btn_install" class="btn btn-warning" disabled>2. Install Update</button>
        <button id="btn_db" class="btn btn-success" disabled>3. Update Database</button>
        <pre id="update_log"
             style="height:300px;overflow:auto;margin-top:15px;background:#f5f5f5;padding:10px;display:none;"></pre>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var log = $('#update_log');
        var base = '<?= base_url() ?>webupdate/';

        function run(action, nextBtn) {
            log.show().append('Running ' + action + ' ...\n');
            $.ajax({url: base + action, type: 'GET', global: false})
                .done(function (output) {
                    log.append(output + '\n');
                    if (nextBtn) $('#' + nextBtn).prop('disabled', false);
                })
                .fail(function (xhr) {
                    log.append('HTTP error ' + xhr.status + '\n');
                });
        }

        $('#btn_check').click(function () {
            $(this).prop('disabled', true);
            run('download_update', 'btn_install');
        });
        $('#btn_install').click(function () {
            $(this).prop('disabled', true);
            run('install_update', 'btn_db');
        });
        $('#btn_db').click(function () {
            $(this).prop('disabled', true);
            run('update_db', null);
        });
    });
</script>
