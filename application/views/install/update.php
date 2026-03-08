<div class="update-wrap">

    <!-- Top Bar -->
    <div class="update-topbar">
        <div class="update-brand">
            <img src="<?= base_url('assets/img/favicon.png') ?>" alt="Logo">
            ASESMEN MADRASAH
        </div>
        <a href="<?= base_url() ?>" class="btn-install btn-install-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Heading -->
    <h4 style="font-size:1rem;font-weight:700;color:#e0e0e0;margin:0 0 1.5rem;text-transform:uppercase;letter-spacing:.5px;">
        Update Database
    </h4>

    <!-- Warning -->
    <div class="install-info" style="margin-bottom:1.5rem;">
        <h5>Sebelum melakukan update</h5>
        <ol>
            <li>Pastikan aplikasi sedang tidak digunakan.</li>
            <li>Backup database terlebih dahulu untuk berjaga-jaga.</li>
        </ol>
    </div>

    <!-- Actions -->
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;margin-bottom:1.25rem;">
        <button id="check" class="btn-install btn-install-primary" onclick="cekDatabase()">
            <i class="fas fa-search"></i> Cek Database
        </button>
        <button id="btn-update" class="btn-install btn-install-success d-none" onclick="updateDatabase()">
            <i class="fas fa-sync-alt"></i> Update Database
        </button>
    </div>

    <!-- Progress -->
    <div id="progress" class="d-none" style="margin-bottom:1rem;">
        <div class="progress-track">
            <div class="progress-bar-custom" id="prog-bar">0%</div>
        </div>
    </div>

    <!-- Spinner -->
    <div id="spinner" class="install-spinner d-none" style="margin-bottom:1rem;">
        <div class="spinner-ring"></div>
        <span id="spinner-info">Mengambil informasi…</span>
    </div>

    <!-- Info -->
    <div id="info-db" class="install-info d-none" style="margin-bottom:1.5rem;"></div>

    <!-- Table results -->
    <div class="row mt-2" id="info-table"></div>

</div>

<?= form_open('update', ['id' => 'update-database']) ?>
<?= form_close() ?>

<script>
    function cekDatabase() {
        $('#check').attr('disabled', true);
        $('#spinner').removeClass('d-none');
        $('#btn-update').addClass('d-none');
        $('#progress').addClass('d-none');
        $('#spinner-info').text('Mengambil informasi…');

        $.ajax({
            type: 'GET',
            url: base_url + 'update/checkdatabase',
            success: function(res) {
                $('#check').removeAttr('disabled').html('<i class="fas fa-redo"></i> Cek Ulang Database');
                $('#spinner').addClass('d-none');
                $('#info-db').removeClass('d-none');

                if (res.counts === 0) {
                    $('#info-db').html('<i class="fas fa-check-circle" style="color:#4ade80;margin-right:.5rem;"></i>Database sudah versi terbaru.');
                } else {
                    $('#info-db').html('<i class="fas fa-exclamation-triangle" style="color:#facc15;margin-right:.5rem;"></i>Database perlu diperbarui ke versi terbaru.');
                    $('#btn-update').removeClass('d-none');
                }
            },
            error: function(xhr) {
                swal.fire({
                    title: 'ERROR',
                    text: 'Ada kesalahan saat pengecekan database.',
                    icon: 'error'
                });
                console.log(xhr.responseText);
            }
        });
    }

    function updateDatabase() {
        $('#check').attr('disabled', true);
        $('#btn-update').attr('disabled', true);
        $('#spinner').removeClass('d-none');
        $('#spinner-info').text('Update database…');
        $('#info-db').html('Update database sedang berjalan…');

        $.ajax({
            method: 'GET',
            url: base_url + 'update/updatedatabase',
            success: function() {
                $('#spinner').addClass('d-none');
                $('#info-db').html('<i class="fas fa-check-circle" style="color:#4ade80;margin-right:.5rem;"></i>Update database selesai.');
                $('#check').removeAttr('disabled');
                $('#btn-update').removeAttr('disabled').addClass('d-none');
            },
            error: function(xhr) {
                swal.fire({
                    title: 'ERROR',
                    html: xhr.responseText,
                    icon: 'error'
                });
                console.log(xhr.responseText);
            }
        });
    }

    function updateProgress(count, message) {
        var prog = Math.round(Number(count));
        $('#prog-bar')
            .css('width', prog + '%')
            .text(prog + '%  ' + message);
        $('#info-db').html(message);
        if (count >= 100) {
            $('#check').removeAttr('disabled');
            $('#btn-update').removeAttr('disabled');
        }
    }
</script>
