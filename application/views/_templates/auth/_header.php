<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login</title>

    <?php $logo_app = $setting->logo_kiri == null
        ? base_url() . 'assets/img/favicon.png'
        : base_url() . $setting->logo_kiri; ?>
    <link rel="shortcut icon" href="<?= $logo_app ?>" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap">

    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/mystyle.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/auth-pages.css">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Lexend', sans-serif !important;
            background: #0a0c10;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.06) 0%, transparent 50%);
            min-height: 100vh;
        }

        body,
        body *:not(i):not([class*="fa"]):not([class*="ion"]):not([class*="bi"]) {
            font-family: 'Lexend', sans-serif !important;
        }
    </style>

    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
</head>

<body class="hold-transition">
