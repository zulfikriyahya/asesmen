<section class="d-flex align-items-center bg-dark height=100%">
    <div class="container">
        <div class="align-content-center">
            <img class="mt-3 mb-4 mr-2" width="25" height="25" src="<?= base_url('assets/img/favicon.png') ?>">
            <span class="text-lg" style="font-family: helvetica"><b>ZEDAPPS CBT</b></span>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-8">
                <h4 style="font-family: helvetica"><b>UPDATE DATABASE</b></h4>
            </div>
            <div class="col-4 text-right">
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    <span ><b> &laquo; Kembali</b></span>
                </a>
            </div>
        </div>
        <div class="alert alert-default-info align-content-center" role="alert">
            <h4 style="font-family: tahoma">Sebelum melakukan update:</h4>
            <ol>
                <li style="font-family: helvetica">Pastikan aplikasi sedang tidak digunakan</li>
                <li style="font-family: helvetica">Backup database terlebih dahulu untuk berjaga-jaga</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-3 col-6">
                <button style="font-family: helvetica" id="check" class="w-100 btn btn-primary" onclick="cekDatabase()">Cek Database</button>
            </div>
            <div id="update" class="col-md-3 col-6 d-none">
                <button id="btn-update" class="w-100 btn btn-success" onclick="updateDatabase()">Update Database
                </button>
            </div>

            <div id="progress" class="col-md-6 col-12 d-none">
                <div class="border border-primary" style="height: 35px">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%; height: 35px">
                    </div>
                </div>
            </div>

            <div id="spinner" class="col-md-6 d-none">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Loading</span>
                </div>
                <span id="spinner-info" class="ml-2">
                    Mengambil informasi ....
                </span>
            </div>
        </div>
        <div id="info-db" class="alert alert-default-info align-content-center mt-3 d-none" role="alert">
        </div>
        <div class="row mt-4" id="info-table">
        </div>
    </div>

    <?= form_open('update', array('id' => 'update-database')) ?>
    <?= form_close() ?>

</section>

<script>
    $(document).ready(function () {

    });

    function cekDatabase() {
        $('#check').attr('disabled', 'disabled');
        $('#spinner').removeClass('d-none');
        $('#update').addClass('d-none');
        $('#progress').addClass('d-none');
        $.ajax({
            type: "GET",
            url: base_url + 'update/checkdatabase',
            success: function (response) {
                console.log(response);
                $('#check').removeAttr('disabled');
                $('#check').text('Cek Ulang Database');
                $('#spinner').addClass('d-none');
                $('#info-db').removeClass('d-none');

                if (response.counts === 0) {
                    console.log('updated');
                    $('#info-db').html('Database sudah versi terbaru');
                } else {
                    console.log('need update');
                    $('#info-db').html('Database perlu update, silahkan lakukan update ke versi terbaru');
                    $('#update').removeClass('d-none');
                }
            },
            error: function (xhr, status, error) {
                console.log("error", xhr.responseText);
                swal.fire({
                    title: "ERROR",
                    text: "Ada kesalahan",
                    icon: "error"
                });
            }
        });
    }

    function updateDatabase() {
        $('#check').attr('disabled', 'disabled');
        $('#btn-update').attr('disabled', 'disabled');

        $('#spinner').removeClass('d-none');
        $('#spinner-info').html('Update database....');
        $('#info-db').html('Update database....');

        $.ajax({
            method: "GET",
            url: base_url + 'update/updatedatabase',
            success: function (response) {
                console.log(response);
                $('#spinner').addClass('d-none');
                $('#info-db').html('Update database selesai');

                $('#check').removeAttr('disabled');
                $('#btn-update').removeAttr('disabled');
                $('#update').addClass('d-none');
            },
            error: function (xhr, status, error) {
                console.log("error", xhr.responseText);
                swal.fire({
                    title: "ERROR",
                    html: xhr.responseText,
                    icon: "error"
                });
            }
        });
    }

    function updateProgress(count, message) {
        var progress = $('.progress-bar');
        var prog = Math.round(Number(count));
        console.log(prog);
        progress.attr('aria-valuenow', prog);
        progress.attr('style', 'width:' + prog + '%; height: 35px');
        progress.html('<span class="text-dark">' + prog + '%  ' + message + '</span>');

        if (count >= 100) {
            $('#check').removeAttr('disabled');
            $('#btn-update').removeAttr('disabled');
        }

        $('#info-db').html(message);
    }

</script>
