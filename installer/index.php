<?php
$base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
$base_url .= '://' . $_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$base_url  = str_replace('installer/', '', $base_url);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASESMEN MADRASAH INSTALLER</title>

    <link rel="shortcut icon" href="<?= $base_url ?>assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= $base_url ?>assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>assets/adminlte/dist/css/adminlte.min.css">

    <script src="<?= $base_url ?>assets/plugins/jquery/jquery.min.js"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #0d0f13;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: .9rem;
            color: #c9cdd4;
        }

        /* ── Split Layout ── */
        .inst-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            width: 100%;
            max-width: 860px;
            align-items: center;
        }

        @media (max-width: 720px) {
            .inst-split {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }
        }

        /* ── Brand ── */
        .inst-brand img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1.25rem;
            filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.5));
        }

        .inst-brand h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #e0e0e0;
            margin: 0 0 .75rem;
        }

        .inst-brand h1 b {
            color: #3d8bfd;
        }

        .inst-brand p {
            font-size: .88rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.6;
        }

        /* ── Card ── */
        .inst-card {
            background: #1a1d23;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
        }

        .inst-card-header {
            background: #111318;
            padding: 1.2rem 1.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .inst-card-header h3 {
            font-size: .85rem;
            font-weight: 700;
            color: #e0e0e0;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .inst-card-body {
            padding: 1.75rem;
        }

        /* ── Form ── */
        .inst-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1.25rem;
        }

        .inst-field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            margin-bottom: 1.1rem;
        }

        .inst-field.full {
            grid-column: 1 / -1;
        }

        .inst-field label {
            font-size: .75rem;
            font-weight: 600;
            color: #9a9fa8;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .inst-field input {
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

        .inst-field input:focus {
            border-color: #3d8bfd;
            box-shadow: 0 0 0 3px rgba(61, 139, 253, 0.15);
        }

        .inst-field input::placeholder {
            color: #3a3f4a;
        }

        .inst-field small {
            font-size: .75rem;
            color: #6c757d;
        }

        /* ── Actions ── */
        .inst-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .btn-inst {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem 1.4rem;
            border: none;
            border-radius: 8px;
            background: #3d8bfd;
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .btn-inst:hover {
            background: #2b7ae0;
        }

        .btn-inst:active {
            transform: scale(.97);
        }

        .btn-inst:disabled {
            opacity: .5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="inst-split">

        <!-- Brand -->
        <div class="inst-brand">
            <img src="<?= $base_url ?>assets/img/favicon.png" alt="Logo">
            <h1><b>ASESMEN</b> MADRASAH</h1>
            <p>Selamat datang di installer ASESMEN MADRASAH.<br>Isi konfigurasi database untuk memulai instalasi.</p>
        </div>

        <!-- Card -->
        <div class="inst-card">
            <div class="inst-card-header">
                <h3>Konfigurasi Database</h3>
            </div>
            <div class="inst-card-body">
                <form action="#" id="create" method="post" accept-charset="utf-8">
                    <div class="inst-grid">

                        <div class="inst-field full">
                            <label>Hostname</label>
                            <input type="text" name="hostname" id="input-nama-host"
                                placeholder="" required>
                        </div>

                        <div class="inst-field">
                            <label>Host Username</label>
                            <input type="text" name="username" id="input-user-host"
                                placeholder="">
                        </div>

                        <div class="inst-field">
                            <label>Host Password</label>
                            <input type="text" name="password" id="input-pass-host"
                                placeholder="">
                            <small>Kosongkan jika tidak menggunakan password.</small>
                        </div>

                        <div class="inst-field full">
                            <label>Nama Database</label>
                            <input type="text" name="database" id="input-nama-db"
                                placeholder="" required>
                            <small>Jangan gunakan spasi.</small>
                        </div>

                    </div>

                    <div class="inst-actions">
                        <button type="submit" id="install-db" class="btn-inst">
                            Install
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="<?= $base_url ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $(function() {
            $('#create').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                swal.fire({
                    title: 'Checking database',
                    text: 'Silahkan tunggu…',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => swal.showLoading()
                });

                $.ajax({
                    url: 'install.php',
                    method: 'POST',
                    data: getFormData($(this)),
                    success: function(response) {
                        var ok = response === '';
                        swal.fire({
                            title: ok ? 'Sukses' : 'Gagal!',
                            html: ok ? 'Database berhasil diinstall' : response,
                            icon: ok ? 'success' : 'error',
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then(function(result) {
                            if (result.value && ok) window.location.href = '<?= $base_url ?>';
                        });
                    },
                    error: function(xhr) {
                        swal.fire({
                            title: 'ERROR',
                            html: 'Gagal inisialisasi database',
                            icon: 'error'
                        });
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        function getFormData($form) {
            var arr = $form.serializeArray(),
                obj = {};
            $.map(arr, function(n) {
                obj[n.name] = n.value;
            });
            return obj;
        }
    </script>
</body>

</html>
