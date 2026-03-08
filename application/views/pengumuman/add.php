<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-6">
                    <h1 class="page-title"><?= $judul ?></h1>
                </div>
                <div class="col-6 text-right">
                    <button onclick="window.history.back();" type="button" class="btn-g danger">
                        <i class="fas fa-arrow-circle-left"></i>
                        <span class="d-none d-sm-inline">Kembali</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="g-card">
                <div class="g-card-header">
                    <h6>Catatan Untuk <strong style="color:var(--accent);"><?= $siswa->nama ?></strong></h6>
                    <button type="button" class="btn-g accent" data-toggle="modal" data-target="#daftarModal">
                        <i class="fa fa-plus"></i> Tambah Catatan
                    </button>
                </div>
                <div class="table-wrap">
                    <div class="table-responsive">
                        <table class="w-100 table table-hover" id="users">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">No.</th>
                                    <th class="text-center">Tanggal</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($catatan_siswa) > 0):
                                    foreach ($catatan_siswa as $key => $value): ?>
                                        <tr>
                                            <td class="text-center"><?= $key + 1 ?></td>
                                            <td class="text-center"><?= $value->tgl ?></td>
                                            <td><?= $value->text ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center" style="padding:2rem;color:var(--text-2);">
                                            Belum ada catatan
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade g-modal" id="daftarModal" tabindex="-1" role="dialog" aria-labelledby="daftarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="daftarLabel">Tambah Catatan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open('', ['id' => 'formcatatan']) ?>
            <div class="modal-body">
                <textarea class="f-input" name="text" id="input_text" rows="4" required
                    placeholder="Tulis catatan…" style="resize:vertical;"></textarea>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id_mapel" value="<?= $id_mapel ?>">
                <input type="hidden" name="id_siswa" value="<?= $siswa->id_siswa ?>">
                <button type="button" class="btn-g" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn-g accent">
                    <i class="fa fa-plus"></i> Simpan
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    var idSiswa = <?= $siswa->id_siswa ?>;
    var idMapel = <?= $id_mapel ?>;
    var idKelas = <?= $id_kelas ?>;

    $(function() {
        $('#formcatatan').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            swal.fire({
                text: 'Silahkan tunggu…',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });
            $.ajax({
                url: base_url + 'kelascatatan/savecatatansiswa',
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(data) {
                    $('#daftarModal').modal('hide');
                    if (data) {
                        swal.fire({
                                title: 'Sukses',
                                text: 'Catatan berhasil disimpan',
                                icon: 'success'
                            })
                            .then(r => {
                                if (r.value) location.href = base_url + 'kelascatatan/siswa?id_siswa=' + idSiswa + '&id_mapel=' + idMapel + '&id_kelas=' + idKelas;
                            });
                    } else {
                        swal.fire({
                            title: 'ERROR',
                            text: 'Catatan tidak tersimpan',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    $('#daftarModal').modal('hide');
                    swal.fire({
                        title: 'Error',
                        text: JSON.parse(xhr.responseText).Message,
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>
