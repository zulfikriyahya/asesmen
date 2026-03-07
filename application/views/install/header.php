<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ZEDAPPS CBT Installer</title>

    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">

    <link href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>/assets/app/css/animate.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/multi-step.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/multi-step-theme.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte/dist/css/adminlte.min.css">

    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/jquery-ui/jquery-ui.min.js"></script>

    <style>
        /* ── Reset & Base ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #0d0f13;
            color: #c9cdd4;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: .9rem;
        }

        /* ── Layout ── */
        .install-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .install-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            width: 100%;
            max-width: 960px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .install-split {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .install-brand {
                text-align: center;
            }
        }

        /* ── Brand ── */
        .install-brand img {
            width: 80px;
            height: auto;
            margin-bottom: 1.25rem;
        }

        .install-brand h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #e0e0e0;
            margin: 0 0 .75rem;
            line-height: 1.3;
        }

        .install-brand h1 b {
            color: #3d8bfd;
        }

        .install-brand p {
            font-size: .95rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.6;
        }

        /* ── Card ── */
        .install-card {
            background: #1a1d23;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
        }

        .install-card-header {
            background: #111318;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .install-card-header h3 {
            font-size: .95rem;
            font-weight: 700;
            color: #e0e0e0;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .install-card-body {
            padding: 1.75rem;
        }

        /* ── Form ── */
        .install-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .1rem 1.25rem;
        }

        @media (max-width: 540px) {
            .install-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .install-field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            margin-bottom: 1.1rem;
        }

        .install-field label {
            font-size: .75rem;
            font-weight: 600;
            color: #9a9fa8;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .install-field input,
        .install-field select {
            background: #111318;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            color: #e0e0e0;
            padding: .65rem .9rem;
            font-size: .88rem;
            width: 100%;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .install-field input:focus,
        .install-field select:focus {
            border-color: #3d8bfd;
            box-shadow: 0 0 0 3px rgba(61, 139, 253, 0.15);
        }

        .install-field input[readonly] {
            opacity: .55;
            cursor: not-allowed;
        }

        .install-field input::placeholder {
            color: #3a3f4a;
        }

        .install-field small {
            font-size: .75rem;
            color: #6c757d;
        }

        .install-field select option {
            background: #1a1d23;
        }

        /* ── Step Indicator ── */
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 1.75rem;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .35rem;
            flex: 1;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 14px;
            left: calc(50% + 14px);
            right: calc(-50% + 14px);
            height: 2px;
            background: rgba(255, 255, 255, 0.08);
            z-index: 0;
        }

        .step-item.completed:not(:last-child)::after {
            background: #3d8bfd;
        }

        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #111318;
            border: 2px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            color: #6c757d;
            position: relative;
            z-index: 1;
            transition: all .25s;
        }

        .step-item.completed .step-dot {
            background: #3d8bfd;
            border-color: #3d8bfd;
            color: #fff;
        }

        .step-item.current .step-dot {
            border-color: #3d8bfd;
            color: #3d8bfd;
            box-shadow: 0 0 0 3px rgba(61, 139, 253, 0.2);
        }

        .step-label {
            font-size: .7rem;
            color: #6c757d;
            font-weight: 500;
            white-space: nowrap;
        }

        .step-item.current .step-label,
        .step-item.completed .step-label {
            color: #3d8bfd;
        }

        /* ── Step Content ── */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        /* ── Divider ── */
        .install-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin: 1.5rem 0 1.25rem;
        }

        /* ── Actions ── */
        .install-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .75rem;
            margin-top: 1.25rem;
        }

        .install-actions .mr-auto {
            margin-right: auto !important;
        }

        /* ── Buttons ── */
        .btn-install {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem 1.25rem;
            border: none;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s, transform .1s;
        }

        .btn-install:active {
            transform: scale(.97);
        }

        .btn-install:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-install-primary {
            background: #3d8bfd;
            color: #fff;
        }

        .btn-install-primary:hover {
            background: #2b7ae0;
            color: #fff;
        }

        .btn-install-success {
            background: #198754;
            color: #fff;
        }

        .btn-install-success:hover {
            background: #146c43;
            color: #fff;
        }

        .btn-install-back {
            background: rgba(255, 255, 255, 0.06);
            color: #c9cdd4;
        }

        .btn-install-back:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        /* ── Alert / Info Box ── */
        .install-info {
            background: rgba(61, 139, 253, 0.08);
            border: 1px solid rgba(61, 139, 253, 0.2);
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            margin-top: 1.5rem;
            color: #c9cdd4;
            font-size: .85rem;
            line-height: 1.7;
        }

        .install-info h5 {
            font-size: .82rem;
            font-weight: 700;
            color: #7eb8ff;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 0 0 .6rem;
        }

        .install-info ol,
        .install-info ul {
            padding-left: 1.25rem;
            margin: .4rem 0 .75rem;
        }

        .install-info li {
            margin-bottom: .3rem;
        }

        .install-info pre {
            background: #111318;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 6px;
            padding: .75rem 1rem;
            font-size: .8rem;
            color: #a8d8a8;
            margin: .5rem 0;
            white-space: pre-wrap;
        }

        /* ── Summary Table ── */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem;
        }

        .summary-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .summary-table tr:last-child {
            border-bottom: none;
        }

        .summary-table td {
            padding: .65rem .5rem;
            vertical-align: top;
        }

        .summary-table td:first-child {
            color: #9a9fa8;
            font-weight: 500;
            width: 40%;
            white-space: nowrap;
        }

        .summary-table td:last-child {
            color: #e0e0e0;
        }

        /* ── Update Page ── */
        .update-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 2.5rem 1rem;
        }

        .update-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .update-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #e0e0e0;
        }

        .update-brand img {
            width: 28px;
            height: 28px;
        }

        .progress-track {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 6px;
            height: 36px;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #3d8bfd, #6ea8fe);
            border-radius: 6px;
            transition: width .3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 600;
            color: #fff;
        }

        /* ── Spinner ── */
        .install-spinner {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: #9a9fa8;
            font-size: .85rem;
        }

        .spinner-ring {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-top-color: #3d8bfd;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Overlay loading ── */
        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(13, 15, 19, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            z-index: 10;
        }

        .overlay.d-none {
            display: none;
        }
    </style>
</head>

<script>
    let base_url = '<?= base_url() ?>';
</script>

<body>
