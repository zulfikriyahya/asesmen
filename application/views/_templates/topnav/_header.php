{{FILE: _templates/topnav/_header.php}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= $judul ?></title>

    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/mystyle.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/pace/pace-theme-flash.css">

    <script src="<?= base_url() ?>assets/bower_components/jquery/jquery-3.3.1.min.js"></script>
    <script src="<?= base_url() ?>assets/bower_components/sweetalert2/sweetalert2.all.min.js"></script>
    <script>
        var base_url = '<?= base_url() ?>';
    </script>
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">

        <header class="main-header">
            <?php require '_menu.php'; ?>
        </header>

        <div class="content-wrapper">
            <div class="container">
                <section class="content-header">
                    <h1><?= $judul ?> <small><?= $subjudul ?></small></h1>
                    <ol class="breadcrumb">
                        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="<?= base_url() ?>ujian/list"><?= $judul ?></a></li>
                        <li class="active"><?= $subjudul ?></li>
                    </ol>
                </section>
                <section class="content">


                    {{FILE: _templates/topnav/_footer.php}}
                </section><!-- /.content -->
            </div><!-- /.container -->
        </div><!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="container">
                <?= strftime('%A, %d %B %Y') ?>, <span class="live-clock"><?= date('H:i:s') ?></span>
                <div class="pull-right hidden-xs"><b>ZEDAPPS SCHOOL</b></div>
            </div>
        </footer>

    </div><!-- /.wrapper -->

    <script src="<?= base_url() ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
    <script src="<?= base_url() ?>assets/bower_components/pace/pace.min.js"></script>

    <script>
        /* ── Countdown helpers ── */
        function sisawaktu(t) {
            var end = new Date(t),
                start = new Date();
            var tid = setInterval(function() {
                var dis = end - Date.now(),
                    h, m, s;
                h = Math.floor((dis % (1000 * 60 * 60 * 60)) / (1000 * 60 * 60));
                m = Math.floor((dis % (1000 * 60 * 60)) / (1000 * 60));
                s = Math.floor((dis % (1000 * 60)) / 1000);
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
                var dis = end - Date.now(),
                    d, h, m, s;
                d = Math.floor(dis / (1000 * 60 * 60 * 24));
                h = Math.floor((dis % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                m = Math.floor((dis % (1000 * 60 * 60)) / (1000 * 60));
                s = Math.floor((dis % (1000 * 60)) / 1000);
                $('.countdown').html(pad(d) + ' Hari, ' + pad(h) + ' Jam, ' + pad(m) + ' Menit, ' + pad(s) + ' Detik');
                setTimeout(function() {
                    location.reload();
                }, dis);
            }, 1000);
        }

        function pad(i) {
            return ('0' + i).slice(-2);
        }

        /* ── CSRF helper ── */
        function ajaxcsrf() {
            var csrf = {};
            csrf['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
            $.ajaxSetup({
                data: csrf
            });
        }

        /* ── Live clock ── */
        $(function() {
            setInterval(function() {
                var d = new Date();
                $('.live-clock').html(pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()));
            }, 1000);
        });
    </script>
</body>

</html>

