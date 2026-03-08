</section><!-- /.content -->
</div><!-- /.container -->
</div><!-- /.content-wrapper -->

<footer class="main-footer" style="
    background: rgba(12,14,20,0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.4);
    font-family: 'Lexend', sans-serif;
    font-size: .78rem;
">
    <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
        <span>
            <?= strftime('%A, %d %B %Y') ?>,
            <span class="live-clock" style="color:rgba(99,102,241,0.9); font-weight:500;"><?= date('H:i:s') ?></span>
        </span>
        <strong style="color:rgba(255,255,255,0.5);">MADRASAH UNIVERSE</strong>
    </div>
</footer>

</div><!-- /.wrapper -->

<script src="<?= base_url() ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
<script src="<?= base_url() ?>assets/bower_components/pace/pace.min.js"></script>

<script>
    function pad(i) {
        return ('0' + i).slice(-2);
    }

    function sisawaktu(t) {
        var end = new Date(t);
        var start = new Date();
        var tid = setInterval(function() {
            var dis = end - Date.now();
            var h = Math.floor((dis % (1000 * 60 * 60 * 60)) / (1000 * 60 * 60));
            var m = Math.floor((dis % (1000 * 60 * 60)) / (1000 * 60));
            var s = Math.floor((dis % (1000 * 60)) / 1000);
            $('.sisawaktu').html(pad(h) + ':' + pad(m) + ':' + pad(s));
        }, 100);
        setTimeout(function() {
            clearInterval(tid);
            waktuHabis();
        }, end - start);
    }

    function countdown(t) {
        var end = new Date(t);
        setInterval(function() {
            var dis = end - Date.now();
            var d = Math.floor(dis / (1000 * 60 * 60 * 24));
            var h = Math.floor((dis % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var m = Math.floor((dis % (1000 * 60 * 60)) / (1000 * 60));
            var s = Math.floor((dis % (1000 * 60)) / 1000);
            $('.countdown').html(pad(d) + ' Hari, ' + pad(h) + ' Jam, ' + pad(m) + ' Menit, ' + pad(s) + ' Detik');
            setTimeout(function() {
                location.reload();
            }, dis);
        }, 1000);
    }

    function ajaxcsrf() {
        var csrf = {};
        csrf['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
        $.ajaxSetup({
            data: csrf
        });
    }

    $(function() {
        setInterval(function() {
            var d = new Date();
            $('.live-clock').html(pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()));
        }, 1000);
    });
</script>

</body>

</html>
