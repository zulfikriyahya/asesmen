<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= $judul ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap">

    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/mystyle.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/pace/pace-theme-flash.css">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body,
        body *:not(i):not([class*="fa"]):not([class*="ion"]):not([class*="bi"]) {
            font-family: 'Lexend', sans-serif !important;
        }

        body {
            background: #0a0c10 !important;
            background-image:
                radial-gradient(ellipse at 15% 40%, rgba(99, 102, 241, 0.06) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 70%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
            color: rgba(255, 255, 255, 0.75);
        }

        .content-wrapper {
            background: transparent !important;
        }

        .content-header h1 {
            font-size: 1.3rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
        }

        .content-header h1 small {
            font-size: .8rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.4);
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
        }

        .breadcrumb a {
            color: #818cf8;
        }

        .breadcrumb>.active {
            color: rgba(255, 255, 255, 0.4);
        }
    </style>

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
