<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= $judul ?></title>

    <?php $logo_app = $setting->logo_kiri == null
        ? base_url() . 'assets/img/favicon.png'
        : base_url() . $setting->logo_kiri; ?>
    <link rel="shortcut icon" href="<?= $logo_app ?>" type="image/x-icon">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/v4-shims.min.css">
    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/bootstrap-icon/bootstrap-icons.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/jqvmap/jqvmap.min.css">
    <!-- Pace -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/pace-progress/themes/silver/pace-theme-center-circle.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Multi Select -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/multiselect/css/multi-select.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Datetime -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/jquery-datetimepicker/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Toast / Toastr -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/jquery.toast.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/show.toast.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Dropify -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/dropify/css/dropify.min.css">
    <!-- Summernote -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/summernote/plugin/audio/summernote-audio.css">
    <!-- KaTeX -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/katex/katex.css">
    <!-- Misc Plugins -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/DualSelectList/css/bala.DualSelectList.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/contextmenu/jquery.contextmenu.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fields-linker/fieldsLinker.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/pignose/css/pignose.calendar.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/ios-switch/component-custom-switch.min.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte/dist/css/poppins.css">
    <!-- Theme -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte/dist/css/adminlte.min.css">
    <!-- App -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/mystyle.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/stylised.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/weekCalendar.css">

    <style>
        page {
            background: white;
            display: block;
            margin: 0 auto 0.5cm;
            box-shadow: none;
        }

        page[size="A4"][layout="potrait"] {
            width: 21.59cm;
            height: 29.7cm;
        }

        page[size="A4"] {
            width: 29.7cm;
            height: 21.59cm;
        }

        .linker-list p {
            margin-bottom: .5rem;
            margin-top: .5rem;
        }
    </style>

    <!-- jQuery -->
    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/DualSelectList/js/bala.DualSelectList.jquery.js"></script>
    <script defer src="<?= base_url() ?>/assets/plugins/katex/contrib/auto-render.min.js"
        onload="renderMathInElement(document.body);"></script>
</head>

<script>
    let base_url = '<?= base_url() ?>';
    let globalToken;
    let adaJadwalUjian;
</script>
<script src="<?= base_url() ?>/assets/app/js/generate.js"></script>
<script>
    let tp_active = '<?= $tp_active->tahun ?>';
    let smt_active = '<?= $smt_active->smt ?>';
    let id_tp_active = '<?= $tp_active->id_tp ?>';
    let id_smt_active = '<?= $smt_active->id_smt ?>';

    /* ── Live clock ── */
    function startTime() {
        var d = new Date(),
            h = d.getHours(),
            m = d.getMinutes(),
            s = d.getSeconds();
        $('#live-clock').html('<span class="text-lg">' + h + ':' + checkTime(m) + '</span>:' + checkTime(s));
        setTimeout(startTime, 1000);
    }

    function checkTime(i) {
        return i < 10 ? '0' + i : i;
    }

    /* ── Date helpers ── */
    var bulans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    var arrhari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];

    function buat_tanggal_indonesia(str) {
        var map = {
            Jan: 'Januari',
            Feb: 'Februari',
            Mar: 'Maret',
            Apr: 'April',
            May: 'Mei',
            Jun: 'Juni',
            Jul: 'Juli',
            Aug: 'Agustus',
            Sep: 'September',
            Oct: 'Oktober',
            Nov: 'November',
            Dec: 'Desember',
            Mon: 'Senin',
            Tue: 'Selasa',
            Wed: 'Rabu',
            Thu: 'Kamis',
            Fri: 'Jumat',
            Sat: 'Sabtu',
            Sun: 'Minggu'
        };
        return str.replace(/\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|Mon|Tue|Wed|Thu|Fri|Sat|Sun)\b/g,
            function(m) {
                return map[m] || m;
            });
    }

    function sanitizeJSON(s) {
        return s.replace(/\\/g, '\\\\').replace(/\n/g, '\\n').replace(/\r/g, '\\r')
            .replace(/\t/g, '\\t').replace(/\f/g, '\\f').replace(/"/g, '\\"')
            .replace(/'/g, "\\'").replace(/&/g, '\\&');
    }

    function stringToDate(str) {
        var p = str.split('-');
        return new Date(p[0], p[1] - 1, p[2]);
    }

    function dateToString(date) {
        return date.getDate().toString().padStart(2, '0') + ' ' +
            bulans[date.getMonth()] + ' ' + date.getFullYear();
    }

    function dateToStringDay(date) {
        return arrhari[date.getDay()] + ', ' + dateToString(date);
    }
</script>

<?php
function buat_tanggal($str)
{
    $map = [
        'Jan' => 'Januari',
        'Feb' => 'Februari',
        'Mar' => 'Maret',
        'Apr' => 'April',
        'May' => 'Mei',
        'Jun' => 'Juni',
        'Jul' => 'Juli',
        'Aug' => 'Agustus',
        'Sep' => 'September',
        'Oct' => 'Oktober',
        'Nov' => 'November',
        'Dec' => 'Desember',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => "Jum'at",
        'Sat' => 'Sabtu',
        'Sun' => 'Minggu'
    ];
    return str_replace(array_keys($map), array_values($map), $str);
}

function singkat_tanggal($str)
{
    $map = [
        'May' => 'Mei',
        'Aug' => 'Agt',
        'Oct' => 'Okt',
        'Dec' => 'Des',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => "Jum'at",
        'Sat' => 'Sabtu',
        'Sun' => 'Minggu'
    ];
    return str_replace(array_keys($map), array_values($map), $str);
}
?>

<body style="background-color:#343a40"
    class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm"
    onload="startTime()">
    <div class="wrapper">
        <?php require_once('navbar.php'); ?>
        <?php require_once('sidebar.php'); ?>
