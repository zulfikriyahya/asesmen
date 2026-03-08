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
                    <div class="alert alert-warning">
                        <strong>Catatan!</strong> BACKUP terlebih dahulu di menu Backup/Restore sebelum melanjutkan.
                    </div>

                    <?php foreach ($tables as $ket => $table) :
                        $keterangan = $ket == '1'
                            ? '<li>Tabel berisi data yang bisa digunakan sepanjang tahun/semester</li><li>Sebaiknya tidak dihapus kecuali data sudah tidak diperlukan</li><li>Tabel otomatis dibackup ketika dihapus</li>'
                            : '<li>Tabel berisi data yang hanya digunakan satu tahun/semester</li><li>Tabel otomatis dibackup ketika dihapus</li>';
                    ?>
                        <div class="alert alert-default-info">
                            <ul class="mb-0"><?= $keterangan ?></ul>
                        </div>
                        <table class="table table-striped table-bordered table-hover mb-5">
                            <thead class="bg-maroon">
                                <tr>
                                    <th width="50" class="text-center align-middle">No.</th>
                                    <th class="text-center align-middle">Tabel</th>
                                    <th class="text-center align-middle">Total Data</th>
                                    <th class="text-center align-middle">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($table as $info) : ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $no ?></td>
                                        <td class="align-middle"><?= $info['name'] ?></td>
                                        <td class="align-middle text-center"><?= $info['size'] ?></td>
                                        <td class="text-center align-middle">
                                            <?php $disable = $info['size'] == 0 ? 'disabled' : ''; ?>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="hapus('<?= $info['table'] ?>')" <?= $disable ?>>
                                                <i class="fa fa-trash mr-1"></i>Kosongkan
                                            </button>
                                        </td>
                                    </tr>
                                <?php $no++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= form_open('', array('id' => 'delete-table')) ?>
<?= form_close() ?>

<script>
    function hapus(src) {
        swal.fire({
            title: 'Anda yakin?',
            html: 'Tabel akan dikosongkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#06b6d4',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Kosongkan!'
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
                    url: base_url + 'dbclear/hapustable',
                    type: 'POST',
                    data: $('#delete-table').serialize() + '&table=' + src,
                    success: function(respon) {
                        swal.fire({
                                title: 'Berhasil',
                                text: respon.message,
                                icon: 'success'
                            })
                            .then(r => {
                                if (r.value) window.location.href = base_url + 'dbclear';
                            });
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
