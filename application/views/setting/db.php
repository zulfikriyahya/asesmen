<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">

<div class="content-wrapper setting-page pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-bold"><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card my-shadow">
                <div class="card-header bg-orange">
                    <div class="card-title">
                        <h6 class="text-bold"><?= $subjudul ?></h6>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <button class="btn btn-primary mb-3" onclick="processBackup()">
                        <i class="fas fa-download mr-1"></i>Backup Semua Data
                    </button>

                    <div class="progress my-4" style="height: 30px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                            style="width:0%">
                        </div>
                    </div>

                    <div class="alert alert-default-info">
                        <ul class="mb-0">
                            <li>Jangan merefresh/menutup halaman ini selama proses backup berlangsung.</li>
                            <li>Jika backup gagal, gunakan database manager untuk membackup file dan database.</li>
                        </ul>
                    </div>

                    <hr>
                    <p class="setting-section-title">Riwayat Backup</p>

                    <?= form_open('', array('id' => 'edittp')) ?>
                    <div class="table-responsive-wrapper">
                        <table id="database" class="table table-striped table-bordered table-hover">
                            <thead class="bg-maroon">
                                <tr>
                                    <th width="50" class="text-center align-middle">No.</th>
                                    <th class="text-center align-middle">Database / File</th>
                                    <th class="text-center align-middle">Ukuran</th>
                                    <th class="text-center align-middle">Tanggal Backup</th>
                                    <th class="text-center align-middle">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                usort($list, function ($a, $b) {
                                    return $b['tgl'] <=> $a['tgl'];
                                });
                                foreach ($list as $key => $value) : ?>
                                    <tr>
                                        <td class="text-center"><?= ($key + 1) ?></td>
                                        <td class="text-center"><?= $value['nama'] . '.' . $value['type'] ?></td>
                                        <td class="text-center"><?= $value['size'] ?></td>
                                        <td class="text-center"><?= buat_tanggal(date('D, d M Y H:i', $value['tgl'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('./backups/' . $value['src']) ?>"
                                                download="<?= $value['src'] ?>"
                                                class="btn btn-xs btn-warning">Download</a>
                                            <button onclick="hapus('<?= $value['src'] ?>')"
                                                class="btn btn-xs btn-danger">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function updateProgress(count, message) {
        var progress = $('.progress-bar');
        progress.attr('aria-valuenow', count);
        progress.attr('style', 'width:' + Number(count) + '%');
        progress.text(count + '%  ' + message);
    }

    function processBackup() {
        swal.fire({
            title: 'Backup sedang berjalan',
            text: 'Silahkan tunggu....',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
                swal.showLoading();
            }
        });
        updateProgress(5, '');
        $.ajax({
            type: 'GET',
            url: base_url + 'dbmanager/backupdb',
            success: function(response) {
                updateProgress(50, response.message);
                backupData();
            }
        });
    }

    function backupData() {
        $.ajax({
            type: 'GET',
            url: base_url + 'dbmanager/backupdata',
            success: function(response) {
                updateProgress(100, response.message);
                swal.fire({
                        title: 'Berhasil',
                        text: 'Semua file data berhasil dibackup',
                        icon: 'success'
                    })
                    .then(r => {
                        if (r.value) window.location.href = base_url + 'dbmanager';
                    });
            }
        });
    }

    function hapus(src) {
        swal.fire({
            title: 'Anda yakin?',
            html: 'File <b>' + src + '</b> akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#06b6d4',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Hapus!'
        }).then(result => {
            if (result.value) {
                swal.fire({
                    text: 'Silahkan tunggu....',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
                $.ajax({
                    url: base_url + 'dbmanager/hapusbackup/' + src,
                    type: 'GET',
                    success: function(respon) {
                        if (respon.status) {
                            swal.fire({
                                    title: 'Berhasil',
                                    text: respon.message,
                                    icon: 'success'
                                })
                                .then(r => {
                                    if (r.value) window.location.href = base_url + 'dbmanager';
                                });
                        } else {
                            swal.fire({
                                title: 'Gagal',
                                text: respon.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        const err = JSON.parse(xhr.responseText);
                        swal.fire({
                            title: 'Error',
                            text: err.Message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
