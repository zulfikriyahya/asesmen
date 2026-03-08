<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <h1><?= $judul ?></h1>
        </div>
    </section>

    <section class="content mb-4">
        <div class="container-fluid">

            <!-- Kelompok & Sub Kelompok -->
            <div class="glass-card mb-4">
                <div class="card-body" style="padding:1.5rem">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span style="font-family:'Lexend',sans-serif;font-size:0.85rem;font-weight:600;color:#67e8f9">Kelompok Utama</span>
                                <button type="button" data-toggle="modal" data-target="#editKelompokModal"
                                    class="btn btn-cyan btn-sm"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="tableKelompok" class="table-glass w-100">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th style="width:80px">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span style="font-family:'Lexend',sans-serif;font-size:0.85rem;font-weight:600;color:#67e8f9">Sub Kelompok</span>
                                <button type="button" data-toggle="modal" data-target="#editSubKelompokModal"
                                    class="btn btn-cyan btn-sm"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="tableSubKelompok" class="table-glass w-100">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Kel. Utama</th>
                                            <th style="width:80px">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Mapel -->
            <div class="glass-card mb-0">
                <div class="card-header">
                    <h6 class="card-title"><?= $subjudul ?></h6>
                    <div class="card-tools">
                        <button type="button" onclick="reload_ajax()" class="btn btn-glass btn-sm">
                            <i class="fa fa-sync mr-1"></i>Reload
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding:1.5rem">
                    <div class="alert-glass mb-3">
                        <b>Nomor Urut Rapor</b> dan <b>Kelompok</b> diperlukan jika ingin mencetak rapor.
                    </div>
                    <?= form_open('', array('id' => 'bulk')) ?>
                    <div class="table-responsive">
                        <table id="tableMapel" class="table-glass w-100">
                            <thead>
                                <tr>
                                    <th style="width:40px"><input type="checkbox" class="select_all"></th>
                                    <th style="width:100px">No.Urut Rapor</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kode Mapel</th>
                                    <th>Kelompok</th>
                                    <th style="width:90px">Status</th>
                                    <th style="width:70px">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal: Kelompok Utama -->
<?= form_open('create', array('id' => 'create-kelompok')) ?>
<div class="modal fade" id="editKelompokModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelompok Mata Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_kel_mapel" name="id_kel_mapel">
                <input type="hidden" name="id_parent" value="0">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kode <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createkodekel" name="kode_kel_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Nama <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createnamakel" name="nama_kel_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kategori <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <select id="kategori" name="kategori" class="form-control form-control-glass" required>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat ?>"><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cyan btn-sm"><i class="fa fa-save mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<!-- Modal: Sub Kelompok -->
<?= form_open('create', array('id' => 'create-sub-kelompok')) ?>
<div class="modal fade" id="editSubKelompokModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sub Kelompok Mata Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_kel_sub" name="id_kel_mapel">
                <input type="hidden" id="kategori_sub" name="kategori">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kode <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createkodesub" name="kode_kel_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Nama <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createnamasub" name="nama_kel_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kel. Utama <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <select id="id_parent_sub" name="id_parent" class="form-control form-control-glass" required>
                            <?php foreach ($kelompok_mapel as $ky => $km): ?>
                                <?php if ($km->id_parent == 0): ?>
                                    <option value="<?= $ky ?>"><?= $km->nama_kel_mapel ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cyan btn-sm"><i class="fa fa-save mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<!-- Modal: Tambah Mapel -->
<?= form_open('create', array('id' => 'create')) ?>
<div class="modal fade" id="createMapelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mapel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Mapel <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createnama" name="nama_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kode <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="createkode" name="kode_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kelompok <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <?= form_dropdown('kelompok', $kelompok, '', 'class="form-control form-control-glass" required') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Status <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <?= form_dropdown('status', $status, '1', 'class="form-control form-control-glass" required') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">No. Urut Rapor</label>
                    <div class="col-md-9">
                        <input type="number" name="urutan_tampil" class="form-control form-control-glass">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cyan btn-sm"><i class="fa fa-save mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<!-- Modal: Edit Mapel -->
<?= form_open('update', array('id' => 'update')) ?>
<div class="modal fade" id="editMapelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mapel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Mapel <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="namaEdit" name="nama_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kode <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <input type="text" id="kodeEdit" name="kode_mapel" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kelompok <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <?= form_dropdown('kelompok', $kelompok, '', 'id="kelompok" class="form-control form-control-glass" required') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Status <span style="color:#f87171">*</span></label>
                    <div class="col-md-9">
                        <?= form_dropdown('status', $status, '1', 'id="status" class="form-control form-control-glass" required') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">No. Urut Rapor</label>
                    <div class="col-md-9">
                        <input type="number" id="kodeUrut" name="urutan_tampil" class="form-control form-control-glass">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="editIdMapel" name="id_mapel">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cyan btn-sm"><i class="fa fa-save mr-1"></i>Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<?= form_open('create', array('id' => 'hapus-kelompok')) ?>
<?= form_close() ?>

<script>
    var jsonkelompokMapel = JSON.parse(JSON.stringify(<?= json_encode($kelompok_mapel) ?>));
    var kelompokMapel = Object.keys(jsonkelompokMapel).map(k => jsonkelompokMapel[k]);

    var table, tableKelompok, tableSubKelompok;

    const btnAksiKelompok = (data) => `
        <div style="display:flex;gap:4px;justify-content:center">
            <a class="btn btn-warning-glass btn-sm" style="padding:0.2rem 0.5rem;font-size:0.72rem"
                data-toggle="modal" data-target="#editKelompokModal"
                data-id="${data.id_kel_mapel}" data-nama="${data.nama_kel_mapel}"
                data-kode="${data.kode_kel_mapel}" data-kategori="${data.kategori}">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <button onclick="hapusKelompok(this)" class="btn btn-danger-glass btn-sm deleteRecord"
                style="padding:0.2rem 0.5rem;font-size:0.72rem"
                data-id="${data.id_kel_mapel}" data-utama="${data.id_parent}" data-kode="${data.kode_kel_mapel}">
                <i class="fa fa-trash"></i>
            </button>
        </div>`;

    const btnAksiSubKelompok = (data) => `
        <div style="display:flex;gap:4px;justify-content:center">
            <a class="btn btn-warning-glass btn-sm" style="padding:0.2rem 0.5rem;font-size:0.72rem"
                data-toggle="modal" data-target="#editSubKelompokModal"
                data-id="${data.id_kel_mapel}" data-nama="${data.nama_kel_mapel}"
                data-kode="${data.kode_kel_mapel}" data-utama="${data.id_parent}" data-kategori="${data.kategori}">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <button onclick="hapusKelompok(this)" class="btn btn-danger-glass btn-sm deleteRecord"
                style="padding:0.2rem 0.5rem;font-size:0.72rem"
                data-id="${data.id_kel_mapel}" data-utama="${data.id_parent}" data-kode="${data.kode_kel_mapel}">
                <i class="fa fa-trash"></i>
            </button>
        </div>`;

    $(document).ready(function() {
        ajaxcsrf();

        tableKelompok = $("#tableKelompok").DataTable({
            searching: false,
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                url: base_url + "datamapel/getdatakelompok",
                type: "POST"
            },
            columns: [{
                    data: "kategori",
                    className: "text-center"
                },
                {
                    data: "kode_kel_mapel",
                    className: "text-center"
                },
                {
                    data: "nama_kel_mapel"
                }
            ],
            columnDefs: [{
                searchable: false,
                targets: 3,
                className: "text-center",
                data: {
                    id_kel_mapel: "id_kel_mapel",
                    nama_kel_mapel: "nama_kel_mapel",
                    kode_kel_mapel: "kode_kel_mapel",
                    id_parent: "id_parent",
                    kategori: "kategori"
                },
                render: (data) => btnAksiKelompok(data)
            }],
            order: [
                [0, "asc"]
            ]
        });

        tableSubKelompok = $("#tableSubKelompok").DataTable({
            searching: false,
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                url: base_url + "datamapel/getdatasubkelompok",
                type: "POST"
            },
            columns: [{
                    data: "kode_kel_mapel",
                    className: "text-center"
                },
                {
                    data: "nama_kel_mapel"
                },
                {
                    data: "kategori",
                    className: "text-center"
                }
            ],
            columnDefs: [{
                searchable: false,
                targets: 3,
                className: "text-center",
                data: {
                    id_kel_mapel: "id_kel_mapel",
                    nama_kel_mapel: "nama_kel_mapel",
                    kode_kel_mapel: "kode_kel_mapel",
                    id_parent: "id_parent",
                    kategori: "kategori"
                },
                render: (data) => btnAksiSubKelompok(data)
            }],
            order: [
                [0, "asc"]
            ]
        });

        var groupColumn = 4;
        table = $("#tableMapel").DataTable({
            dom: "<'row'<'toolbar col-sm-6'lfrtip><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            processing: true,
            serverSide: true,
            paging: false,
            ajax: {
                url: base_url + "datamapel/read",
                type: "POST"
            },
            columns: [{
                    data: "id_mapel",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "urutan_tampil",
                    className: "text-center",
                    searchable: false
                },
                {
                    data: "nama_mapel"
                },
                {
                    data: "kode",
                    className: "text-center"
                },
                {
                    data: "kelompok",
                    className: "text-center"
                },
                {
                    data: "status",
                    className: "text-center"
                }
            ],
            columnDefs: [{
                    targets: 0,
                    data: null,
                    render: (data, type, row) =>
                        `<div class="text-center"><input id="check${row.id_mapel}" name="checked[]" class="check" value="${row.id_mapel}" type="checkbox"></div>`
                },
                {
                    searchable: false,
                    targets: 6,
                    data: {
                        id_mapel: "id_mapel",
                        nama_mapel: "nama_mapel",
                        kode: "kode",
                        kelompok: "kelompok",
                        deletable: "deletable",
                        status: "status",
                        urutan_tampil: "urutan_tampil"
                    },
                    render: (data) =>
                        `<div class="text-center">
                            <a class="btn btn-warning-glass btn-sm" style="padding:0.2rem 0.5rem;font-size:0.72rem"
                                data-toggle="modal" data-target="#editMapelModal"
                                data-deletable="${data.deletable}" data-status="${data.status}"
                                data-id="${data.id_mapel}" data-nama="${data.nama_mapel}"
                                data-kode="${data.kode}" data-kelompok="${data.kelompok}"
                                data-urutan="${data.urutan_tampil}">
                                <i class="fa fa-pencil-alt"></i>
                            </a>
                        </div>`
                }
            ],
            order: [
                [groupColumn, 'asc']
            ],
            rowId: (a) => a,
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;
                api.column(groupColumn, {
                    page: 'current'
                }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            `<tr style="background:rgba(6,182,212,0.08)"><td class="pl-3" colspan="7" style="font-family:'Lexend',sans-serif;font-size:0.8rem;font-weight:600;color:#67e8f9;padding:0.5rem 1rem">${group}</td></tr>`
                        );
                        last = group;
                    }
                });
            },
            rowCallback: function(row, data) {
                var st = data.status === '0' ?
                    '<span class="badge-danger-glass">Nonaktif</span>' :
                    '<span class="badge-success-glass">Aktif</span>';
                $("td:eq(5)", row).html(st);
            }
        });

        $("div.toolbar").html(
            `<button id="hapusterpilih" onclick="bulk_delete()" type="button" class="btn btn-danger-glass btn-sm mr-2 mb-2 d-none"><i class="far fa-trash-alt mr-1"></i>Hapus Terpilih</button>` +
            `<button type="button" data-toggle="modal" data-target="#createMapelModal" class="btn btn-cyan btn-sm mr-1 mb-2"><i class="fa fa-plus mr-1"></i>Tambah Data</button>` +
            `<a href="${base_url}datamapel/import" class="btn btn-success-glass btn-sm mr-1 mb-2"><i class="fa fa-upload mr-1"></i>Import</a>`
        );

        $('#id_parent_sub').on('change', function() {
            var idx = kelompokMapel.map(k => k.id_kel_mapel).indexOf($(this).val());
            if (idx > -1) $('#kategori_sub').val(kelompokMapel[idx].kategori);
        });

        $('#editKelompokModal').on('show.bs.modal', function(e) {
            $('#createnamakel').val($(e.relatedTarget).data('nama'));
            $('#createkodekel').val($(e.relatedTarget).data('kode'));
            $('#id_kel_mapel').val($(e.relatedTarget).data('id'));
            $('#kategori').val($(e.relatedTarget).data('kategori'));
        });

        $('#editSubKelompokModal').on('show.bs.modal', function(e) {
            $('#createnamasub').val($(e.relatedTarget).data('nama'));
            $('#createkodesub').val($(e.relatedTarget).data('kode'));
            $('#id_kel_sub').val($(e.relatedTarget).data('id'));
            $('#id_parent_sub').val($(e.relatedTarget).data('utama'));
            $('#kategori_sub').val($(e.relatedTarget).data('kategori'));
        });

        function submitKelompok(formId, modalId) {
            $(formId).on('submit', function() {
                $.ajax({
                    url: base_url + "datamapel/addkelompokmapel",
                    type: "POST",
                    dataType: "JSON",
                    data: $(this).serialize(),
                    success: function() {
                        $(modalId).modal('hide').data('bs.modal', null);
                        showSuccessToast('Data berhasil disimpan.');
                        setTimeout(() => window.location.reload(true), 1000);
                    },
                    error: function() {
                        $(modalId).modal('hide').data('bs.modal', null);
                        showDangerToast("Gagal menyimpan data");
                    }
                });
                return false;
            });
        }
        submitKelompok('#create-kelompok', '#editKelompokModal');
        submitKelompok('#create-sub-kelompok', '#editSubKelompokModal');

        $(".select_all").on("click", function() {
            var checked = this.checked;
            $(".check").each(function() {
                this.checked = checked;
            });
            $('#hapusterpilih').toggleClass('d-none', !checked);
        });

        $("#tableMapel tbody").on("click", "tr .check", function() {
            var total = $("#tableMapel tbody tr .check").length;
            var checked = $("#tableMapel tbody tr .check:checked").length;
            $(".select_all").prop("checked", total === checked);
            $('#hapusterpilih').toggleClass('d-none', checked === 0);
        });

        $('#create').on('submit', function() {
            $.ajax({
                url: base_url + "datamapel/create",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function() {
                    $('#createMapelModal').modal('hide').data('bs.modal', null);
                    showSuccessToast('Data berhasil disimpan.');
                    table.ajax.reload();
                },
                error: function() {
                    $('#createMapelModal').modal('hide').data('bs.modal', null);
                    showDangerToast("Gagal menyimpan data");
                }
            });
            return false;
        });

        $('#editMapelModal').on('show.bs.modal', function(e) {
            var t = e.relatedTarget;
            $('#namaEdit').val($(t).data('nama'));
            $('#kodeEdit').val($(t).data('kode'));
            $('#editIdMapel').val($(t).data('id'));
            $('#kelompok').val($(t).data('kelompok'));
            $('#status').val($(t).data('status'));
            $('#kodeUrut').val($(t).data('urutan'));
        });

        $('#update').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $.ajax({
                url: base_url + "datamapel/update",
                data: $(this).serialize(),
                method: 'POST',
                dataType: "JSON",
                success: function() {
                    $('#editMapelModal').modal('hide').data('bs.modal', null);
                    showSuccessToast('Data berhasil diupdate.');
                    table.ajax.reload();
                },
                error: function() {
                    $('#editMapelModal').modal('hide').data('bs.modal', null);
                    showDangerToast('Error');
                }
            });
        });

        $("#bulk").on("submit", function(e) {
            if ($(this).attr("action") == base_url + "datamapel/delete") {
                e.preventDefault();
                e.stopImmediatePropagation();
                $.ajax({
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    type: "POST",
                    success: function(respon) {
                        swal.fire({
                            title: respon.status ? "Berhasil" : "Gagal",
                            html: respon.status ? respon.total + " data berhasil dihapus" : respon.total,
                            icon: respon.status ? "success" : "error"
                        });
                        reload_ajax();
                    },
                    error: function() {
                        swal.fire({
                            title: "Gagal",
                            text: "Ada data yang sedang digunakan",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });

    function deleteItem(id) {
        dismissEdit();
        var cb = document.getElementById("check" + id);
        if (cb) {
            cb.checked = true;
            bulk_delete("check" + id);
        }
    }

    function dismissEdit() {
        $("#tableMapel tr .check").each(function() {
            this.checked = false;
        });
    }

    function bulk_delete(id) {
        if (!$("#tableMapel tbody tr .check:checked").length) {
            return swal.fire({
                title: "Gagal",
                text: "Tidak ada data yang dipilih",
                icon: "error"
            });
        }
        $("#bulk").attr("action", base_url + "datamapel/delete");
        swal.fire({
            title: "Anda yakin?",
            text: "Data akan dihapus!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0891b2",
            cancelButtonColor: "#d33",
            confirmButtonText: "Hapus!"
        }).then(result => {
            if (result.value) $("#bulk").submit();
            else if (id) {
                var el = document.getElementById(id);
                if (el) el.checked = false;
            }
        });
    }

    function hapusKelompok(e) {
        var id = $(e).data('id');
        var kode = $(e).data('kode');
        var parent = $(e).data('utama');
        var dataPost = $('#hapus-kelompok').serialize() + '&id_kel=' + id + '&id_parent=' + parent + '&kode=' + kode;

        swal.fire({
            title: "Anda yakin?",
            text: "Data Kelompok Mapel akan dihapus!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0891b2",
            cancelButtonColor: "#d33",
            confirmButtonText: "Hapus!"
        }).then(result => {
            if (result.value) {
                $.ajax({
                    url: base_url + "datamapel/hapuskelompok",
                    type: "POST",
                    data: dataPost,
                    success: function(data) {
                        if (data.status) {
                            swal.fire({
                                    title: "Berhasil",
                                    text: "Data berhasil dihapus",
                                    icon: "success"
                                })
                                .then(r => {
                                    if (r.value) {
                                        tableKelompok.ajax.reload();
                                        tableSubKelompok.ajax.reload();
                                    }
                                });
                        } else {
                            swal.fire({
                                title: "Gagal",
                                html: data.message,
                                icon: "error"
                            });
                        }
                    },
                    error: function() {
                        showDangerToast();
                    }
                });
            }
        });
    }
</script>
