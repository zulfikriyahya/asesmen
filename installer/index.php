<?php
$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://" . $_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
$base_url = str_replace('installer/', '', $base_url);
?>

<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ZEDAPPS CBT Installer</title>

    <link rel="shortcut icon" href="<?= $base_url ?>assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= $base_url ?>assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>assets/adminlte/dist/css/adminlte.min.css">

    <script src="<?= $base_url ?>assets/plugins/jquery/jquery.min.js"></script>

<body id="page-top" class="bg-dark align-content-center">
<section class="d-flex align-items-center">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 d-lg-flex flex-lg-column justify-content-center align-items-stretch pt-lg-0 aos-init aos-animate" data-aos="fade-up">
                <div class="text-center">
                    <div>
                        <img class="mb-4 mr-2" width="100" height="100" src="<?= $base_url ?>assets/img/favicon.png">
                        <span class="h1 ml-4" style="font-family: helvetica"><b>ZEDAPPS CBT</b></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-lg-flex flex-lg-column align-items-stretch hero-img aos-init aos-animate" data-aos="fade-up">
                <div class="card" style="background-color: rgba(255,255,255);">
                    <div class="card-body text-dark">
                        <form action="#" id="create" method="post" accept-charset="utf-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="input-nama-db" style="font-family: helvetica">Hostname</label>
                                    <input style="font-family: helvetica" type="text" class="form-control db" id="input-nama-host" name="hostname" value="" placeholder="localhost" required="">
                                </div>
                                <div class="form-group col-6" required="">
                                    <label for="input-nama-db" style="font-family: helvetica">Host Username</label>
                                    <input style="font-family: helvetica" type="text" class="form-control db" id="input-user-host" name="username" value="" placeholder="root">
                                </div>
                                <div class="form-group col-6" required="">
                                    <label for="input-nama-db" style="font-family: helvetica">Host Password</label>
                                    <input style="font-family: helvetica" type="text" class="form-control" id="input-pass-host" value="" name="password" placeholder="">
                                    <small style="font-family: helvetica" class="form-text text-muted">Kosongkan jika tidak menggunakan password.
                                    </small>
                                </div>
                                <div class="form-group col-12" required="">
                                    <label for="input-nama-db" style="font-family: helvetica">Nama Database</label>
                                    <input style="font-family: helvetica" type="text" class="form-control db" id="input-nama-db" name="database" value="" placeholder="database">
                                    <small style="font-family: helvetica" class="form-text text-muted">Jangan gunakan spasi.</small>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-end">
                                <button type="submit" id="install-db" class="btn-primary btn-sm" style="font-family: helvetica"><b>INSTALL</b></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('#create').submit(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            console.log("data:", $(this).serialize());

            swal.fire({
                title: "Checking database",
                text: "Silahkan tunggu....",
                button: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                }
            });

            var $form = $(this);
            var data = getFormData($form);
            console.log(data);

            $.ajax({
                url: 'install.php',
                method: 'POST',
                data: data,
                success: function (response) {
                    console.log(response);
                    const isSuccess = response == '';
                    swal.close();
                    swal.fire({
                        title: isSuccess ? "Sukses" : "Gagal!",
                        html: isSuccess ? "Database berhasil diinstall" : response,
                        icon: isSuccess ? "success" : "error",
                        closeOnClickOutside: false,
                        showCancelButton: false,
                        closeOnEsc: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    }).then(result => {
                        if (result.value) {
                            if (isSuccess) window.location.href = '<?= $base_url ?>';
                        }
                    });
                },
                error: function (xhr, error, status) {
                    swal.close();
                    Swal.fire({
                        title: "ERROR",
                        html: 'Gagal inisialisasi database',
                        icon: "error"
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    });

    function getFormData($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
</script>

<script src="<?= $base_url ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>

</body>
</html>
