<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <h1><?= $judul ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h6 class="card-title"><?= $subjudul ?></h6>
                    <div class="card-tools">
                        <button type="button" onclick="reload_ajax()" class="btn btn-glass btn-sm">
                            <i class="fa fa-sync mr-1"></i>Reload
                        </button>
                        <button type="button" data-from="add" data-toggle="modal" data-target="#createTahunModal"
                            class="btn btn-cyan btn-sm">
                            <i class="fas fa-plus mr-1"></i>Tambah Tahun Pelajaran
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding:1.5rem">
                    <div class="row">
                        <!-- Tabel Tahun Pelajaran -->
                        <div class="col-md-7 mb-4">
                            <p style="font-family:'Lexend',sans-serif;font-size:0.82rem;font-weight:600;color:#67e8f9;margin-bottom:0.6rem">
                                Tahun Pelajaran
                            </p>
                            <?= form_open('', array('id' => 'edittp')) ?>
                            <div class="table-responsive">
                                <table id="tahun" class="table-glass w-100">
                                    <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th style="width:50px">No.</th>
                                            <th>Tahun Pelajaran</th>
                                            <th style="width:120px">Status</th>
                                            <th style="width:120px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tp as $key => $value): ?>
                                            <tr>
                                                <td class="d-none"><?= $value->id_tp ?></td>
                                                <td class="text-center"><?= ($key + 1) ?></td>
                                                <td class="text-center"><?= $value->tahun ?></td>
                                                <td class="text-center">
                                                    <?php if ($value->active): ?>
                                                        <span style="font-family:'Lexend',sans-serif;font-size:0.78rem;color:#6ee7b7">
                                                            <i class="fa fa-check mr-1"></i>AKTIF
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button" data-id="<?= $value->id_tp ?>"
                                                            class="btn btn-cyan btn-sm btn-aktif" style="font-size:0.72rem;padding:0.2rem 0.6rem">
                                                            AKTIFKAN
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div style="display:flex;gap:4px;justify-content:center">
                                                        <button type="button"
                                                            data-id="<?= $value->id_tp ?>"
                                                            data-tahun="<?= $value->tahun ?>"
                                                            data-from="edit"
                                                            data-toggle="modal"
                                                            data-target="#createTahunModal"
                                                            class="btn btn-warning-glass btn-sm btn-edit"
                                                            style="font-size:0.72rem;padding:0.2rem 0.6rem">
                                                            <i class="fa fa-pencil-alt mr-1"></i>Edit
                                                        </button>
                                                        <button type="button"
                                                            data-id="<?= $value->id_tp ?>"
                                                            class="btn btn-danger-glass btn-sm btn-hapus"
                                                            style="font-size:0.72rem;padding:0.2rem 0.6rem">
                                                            <i class="fa fa-trash mr-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?= form_close() ?>
                        </div>

                        <!-- Tabel Semester -->
                        <div class="col-md-5 mb-4">
                            <p style="font-family:'Lexend',sans-serif;font-size:0.82rem;font-weight:600;color:#67e8f9;margin-bottom:0.6rem">
                                Semester
                            </p>
                            <?= form_open('', array('id' => 'editsmt')) ?>
                            <div class="table-responsive">
                                <table id="semester" class="table-glass w-100">
                                    <thead>
                                        <tr>
                                            <th class="d-none">id</th>
                                            <th style="width:50px">No.</th>
                                            <th>Semester</th>
                                            <th style="width:120px">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($smt as $key => $value): ?>
                                            <tr>
                                                <td class="d-none"><?= $value->id_smt ?></td>
                                                <td class="text-center"><?= ($key + 1) ?></td>
                                                <td class="text-center"><?= $value->smt ?></td>
                                                <td class="text-center">
                                                    <?php if ($value->active): ?>
                                                        <span style="font-family:'Lexend',sans-serif;font-size:0.78rem;color:#6ee7b7">
                                                            <i class="fa fa-check mr-1"></i>AKTIF
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button" data-id="<?= $value->id_smt ?>"
                                                            class="btn btn-cyan btn-sm btn-aktif" style="font-size:0.72rem;padding:0.2rem 0.6rem">
                                                            AKTIFKAN
                                                        </button>
                                                    <?php endif; ?>
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
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah/Edit Tahun -->
<?= form_open('', array('id' => 'create')) ?>
<div class="modal fade" id="createTahunModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Tahun</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Tahun <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createtahun" name="tahun" class="form-control form-control-glass" required placeholder="contoh: 2024/2025">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="editIdTahun" class="form-control">
                <input type="hidden" id="method" name="method" class="form-control">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cyan btn-sm" id="submit-tp">
                    <i class="fa fa-save mr-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson-cell.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson-row.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/plugins/jquery-table2json/src/tabletojson.js"></script>
<script>
    $(document).ready(function() {

        $("#tahun").on("click", ".btn-aktif", function() {
            let id = $(this).data("id");
            var dataTahun = JSON.stringify($('#tahun').tableToJSON());
            var replaced = dataTahun.replace(/Tahun Pelajaran/g, "tp");

            swal.fire({
                text: "Silahkan tunggu....",
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });
            $.ajax({
                url: base_url + "datatahun/gantitahun",
                data: $('#edittp').serialize() + "&active=" + id + "&tahun=" + replaced,
                type: "POST",
                success: function(response) {
                    swal.fire({
                        title: response.status ? "Berhasil" : "Gagal",
                        text: response.msg,
                        icon: response.status ? "success" : "error"
                    }).then(result => {
                        if (result.value && response.status) window.location.href = base_url + 'datatahun';
                    });
                },
                error: function(xhr) {
                    swal.fire({
                        title: "Error",
                        text: JSON.parse(xhr.responseText).Message,
                        icon: "error"
                    });
                }
            });
        });

        $('#createTahunModal').on('show.bs.modal', function(e) {
            var method = $(e.relatedTarget).data('from');
            $(e.currentTarget).find('#method').val(method);

            if (method === 'edit') {
                $('#createModalLabel').text('Edit Tahun');
                var id = $(e.relatedTarget).data('id');
                var tahun = $(e.relatedTarget).data('tahun');
                $(e.currentTarget).find('#editIdTahun').val(id).attr('name', 'id_tahun');
                $(e.currentTarget).find('#createtahun').val(tahun);
            } else {
                $('#createModalLabel').text('Tambah Tahun');
                $(e.currentTarget).find('#editIdTahun').val('');
                $(e.currentTarget).find('#createtahun').val('');
            }
        });

        $("#tahun").on("click", ".btn-hapus", function() {
            let id = $(this).data("id");
            swal.fire({
                title: 'Hapus Tahun',
                text: 'Anda yakin akan menghapus Tahun Pelajaran? Tindakan ini akan membuat data yang berhubungan tidak aktif.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Hapus"
            }).then(result => {
                if (result.value) {
                    $.ajax({
                        url: base_url + "datatahun/hapustahun",
                        data: $('#edittp').serialize() + "&hapus=" + id,
                        type: "POST",
                        success: function(response) {
                            swal.fire({
                                title: response.status ? "Berhasil" : "Gagal",
                                text: response.msg,
                                icon: response.status ? "success" : "error"
                            }).then(r => {
                                if (r.value && response.status) window.location.href = base_url + 'datatahun';
                            });
                        },
                        error: function(xhr) {
                            swal.fire({
                                title: "Error",
                                text: JSON.parse(xhr.responseText).Message,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });

        $("#semester").on("click", ".btn-aktif", function() {
            let id = $(this).data("id");
            var dataSmt = JSON.stringify($('#semester').tableToJSON());
            $.ajax({
                url: base_url + "datatahun/gantisemester",
                data: $('#edittp').serialize() + "&active=" + id + "&semester=" + dataSmt,
                type: "POST",
                success: function(response) {
                    swal.fire({
                        title: response.status ? "Berhasil" : "Gagal",
                        text: response.msg,
                        icon: response.status ? "success" : "error"
                    }).then(r => {
                        if (r.value && response.status) window.location.href = base_url + 'datatahun';
                    });
                },
                error: function(xhr) {
                    swal.fire({
                        title: "Error",
                        text: JSON.parse(xhr.responseText).Message,
                        icon: "error"
                    });
                }
            });
        });

        $('#create').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var form = new FormData($('#create')[0]);
            $.ajax({
                url: base_url + "datatahun/add",
                type: "POST",
                data: form,
                processData: false,
                contentType: false,
                cache: false,
                success: function() {
                    location.href = base_url + 'datatahun';
                },
                error: function() {
                    $('#createTahunModal').modal('hide').data('bs.modal', null);
                    showDangerToast('Gagal menambah tahun pelajaran');
                }
            });
            return false;
        });
    });
</script>
